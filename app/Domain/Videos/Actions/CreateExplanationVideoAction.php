<?php

namespace App\Domain\Questions\Actions;

use App\Domain\Questions\Models\Question;
use App\Domain\Questions\Models\QuestionReport;
use App\Domain\Auth\Models\User;
use App\Domain\Content\Models\Pdf;
use App\Events\NewReportEvent; // <-- تمت الإضافة
use Illuminate\Support\Facades\DB;

class ReportQuestionAction
{
    public function execute(Pdf $pdf, int $questionNumber, User $student, ?string $questionText = null): QuestionReport
    {
        return DB::transaction(function () use ($pdf, $questionNumber, $student, $questionText) {
            $question = Question::firstOrCreate(
                ['pdf_id' => $pdf->id, 'question_number' => $questionNumber],
                ['reports_count' => 0]
            );

            try {
                $report = QuestionReport::create([
                    'question_id' => $question->id,
                    'student_id' => $student->id,
                    'question_text' => $questionText,
                ]);

                $question->increment('reports_count');
                $question->update(['last_reported_at' => now()]);

                // <-- إطلاق الحدث لإشعار المدرس
                event(new NewReportEvent($report));

                return $report;
            } catch (\Illuminate\Database\QueryException $e) {
                if ($this->isUniqueViolation($e)) {
                    return QuestionReport::where('question_id', $question->id)
                        ->where('student_id', $student->id)
                        ->first();
                }
                throw $e;
            }
        });
    }

    private function isUniqueViolation(\Illuminate\Database\QueryException $e): bool
    {
        $errorCode = $e->errorInfo[1] ?? null;
        return $errorCode === 23000 || str_contains($e->getMessage(), 'Unique constraint');
    }
}