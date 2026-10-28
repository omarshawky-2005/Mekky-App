<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Domain\Content\Models\Pdf;
use App\Domain\Questions\Actions\ReportQuestionAction;
use App\Domain\Questions\Models\QuestionReport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Student\ReportQuestionRequest;
use App\Http\Resources\Questions\QuestionReportResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class QuestionReportController extends Controller
{
    protected ReportQuestionAction $reportAction;

    public function __construct(ReportQuestionAction $reportAction)
    {
        $this->reportAction = $reportAction;
    }

    public function store(ReportQuestionRequest $request, Pdf $pdf): JsonResponse
    {
        $report = $this->reportAction->execute(
            $pdf,
            $request->question_number,
            $request->user(),
            $request->question_text
        );

        return ApiResponse::success(new QuestionReportResource($report), 'تم الإبلاغ عن السؤال بنجاح', 201);
    }

    public function index(\Illuminate\Http\Request $request): JsonResponse
    {
        $reports = QuestionReport::where('student_id', $request->user()->id)
            ->with('question.pdf.lesson.chapter.course') // تحميل العلاقات لتجنب N+1
            ->latest()
            ->paginate(15);

        return ApiResponse::success(QuestionReportResource::collection($reports));
    }
}