<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Domain\Questions\Models\Question;
use App\Domain\Questions\Models\QuestionReport;
use App\Domain\Videos\Models\ExplanationVideo;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $teacherId = $request->user()->id;
        $cacheKey = "teacher:{$teacherId}:dashboard_stats";

        // كاش لمدة 5 دقائق (300 ثانية)
        $stats = Cache::remember($cacheKey, 300, function () use ($teacherId) {
            
            // 1. النظرة العامة (Overview)
            $totalStudents = \App\Domain\Auth\Models\User::where('role', 'student')->count();
            
            $totalQuestions = Question::whereHas('pdf.lesson.chapter.course', function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })->count();

            $totalReportedQuestions = Question::whereHas('pdf.lesson.chapter.course', function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })->where('reports_count', '>', 0)->count();

            $totalVideos = ExplanationVideo::where('teacher_id', $teacherId)->count();
            $publishedVideos = ExplanationVideo::where('teacher_id', $teacherId)->where('is_published', true)->count();

            $newReportsToday = QuestionReport::whereHas('question.pdf.lesson.chapter.course', function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })->whereDate('created_at', today())->count();

            // 2. الأسئلة الأكثر بلاغاً (Top 10)
            $topReported = Question::whereHas('pdf.lesson.chapter.course', function ($q) use ($teacherId) {
                $q->where('teacher_id', $teacherId);
            })
            ->with(['pdf.lesson.chapter.course:id,title', 'pdf:id,lesson_id,title'])
            ->orderByDesc('reports_count')
            ->limit(10)
            ->get(['id', 'question_number', 'reports_count', 'pdf_id'])
            ->map(function ($q) {
                return [
                    'question_id' => $q->id,
                    'question_number' => $q->question_number,
                    'reports_count' => $q->reports_count,
                    'course_title' => $q->pdf->lesson->chapter->course->title ?? 'Unknown',
                    'pdf_title' => $q->pdf->title ?? 'Unknown',
                ];
            });

            return [
                'overview' => [
                    'total_students' => $totalStudents,
                    'total_questions' => $totalQuestions,
                    'total_reported_questions' => $totalReportedQuestions,
                    'total_videos' => $totalVideos,
                    'published_videos' => $publishedVideos,
                    'new_reports_today' => $newReportsToday,
                ],
                'top_reported_questions' => $topReported,
            ];
        });

        return ApiResponse::success($stats);
    }
}