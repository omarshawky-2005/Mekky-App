<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Domain\Questions\Models\Question;
use App\Http\Controllers\Controller;
use App\Http\Resources\Questions\QuestionWithReportsResource;
use App\Http\Resources\Questions\QuestionReportResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QuestionDashboardController extends Controller
{
    /**
     * داشبورد المدرس: الأسئلة مرتبة بـ reports_count مع فلاتر
     */
    public function index(Request $request): JsonResponse
    {
        $query = Question::query()
            ->with(['pdf.lesson.chapter.course', 'reports.student']) // تحميل العلاقات
            ->orderByDesc('reports_count');

        // تطبيق الفلاتر (كورس / درس)
        if ($request->filled('course_id')) {
            $query->whereHas('pdf.lesson.chapter.course', function ($q) use ($request) {
                $q->where('id', $request->course_id);
            });
        }

        if ($request->filled('lesson_id')) {
            $query->whereHas('pdf.lesson', function ($q) use ($request) {
                $q->where('id', $request->lesson_id);
            });
        }

        $questions = $query->paginate(20);

        return ApiResponse::success(QuestionWithReportsResource::collection($questions));
    }

    /**
     * تفاصيل السؤال: قائمة الطلاب اللي بلّغوا
     */
    public function show(int $id): JsonResponse
    {
        $question = Question::with(['reports.student', 'pdf.lesson.chapter.course'])->findOrFail($id);

        // التأكد من أن المدرس يملك الكورس (يمكن استخدام Policy هنا)
        // Gate::authorize('view', $question->pdf->lesson->course);

        $reports = $question->reports()->latest()->paginate(20);

        return ApiResponse::success([
            'question' => new QuestionWithReportsResource($question),
            'reports' => QuestionReportResource::collection($reports),
        ]);
    }
}