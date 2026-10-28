<?php

namespace App\Domain\Questions\Actions;

use App\Domain\Questions\Models\Question;
use App\Domain\Questions\Models\QuestionReport;
use App\Domain\Auth\Models\User;
use App\Domain\Content\Models\Pdf;
use Illuminate\Support\Facades\DB;

class ReportQuestionAction
{
    /**
     * تنفيذ عملية الإبلاغ عن سؤال
     *
     * @param Pdf $pdf
     * @param int $questionNumber
     * @param User $student
     * @param string|null $questionText
     * @return QuestionReport
     */
    public function execute(Pdf $pdf, int $questionNumber, User $student, ?string $questionText = null): QuestionReport
    {
        return DB::transaction(function () use ($pdf, $questionNumber, $student, $questionText) {
            // 1. إنشاء أو جلب السؤال (يعتمد على Unique Constraint: pdf_id + question_number)
            $question = Question::firstOrCreate(
                [
                    'pdf_id' => $pdf->id,
                    'question_number' => $questionNumber,
                ],
                [
                    'reports_count' => 0,
                ]
            );

            // 2. محاولة إنشاء البلاغ (يعتمد على Unique Constraint: question_id + student_id)
            // نستخدم try-catch للتعامل مع Race Conditions إذا حاول الطالب الإبلاغ مرتين في نفس اللحظة
            try {
                $report = QuestionReport::create([
                    'question_id' => $question->id,
                    'student_id' => $student->id,
                    'question_text' => $questionText,
                ]);

                // 3. زيادة عدد البلاغات وتحديث آخر بلاغ فقط إذا تم إنشاء بلاغ جديد
                $question->increment('reports_count');
                $question->update(['last_reported_at' => now()]);

                return $report;
            } catch (\Illuminate\Database\QueryException $e) {
                // التحقق من أنه خطأ Unique Violation (كود 23000 في MySQL)
                if ($this->isUniqueViolation($e)) {
                    // الطالب بلغ بالفعل عن هذا السؤال، نرجع البلاغ الموجود
                    return QuestionReport::where('question_id', $question->id)
                        ->where('student_id', $student->id)
                        ->first();
                }
                throw $e;
            }
        });
    }

    /**
     * التحقق من أن الخطأ هو Unique Violation
     */
    private function isUniqueViolation(\Illuminate\Database\QueryException $e): bool
    {
        $errorCode = $e->errorInfo[1] ?? null;
        // 23000 هو كود Integrity constraint violation في MySQL
        return $errorCode === 23000 || str_contains($e->getMessage(), 'Unique constraint');
    }
}