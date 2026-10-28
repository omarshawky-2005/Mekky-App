<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Domain\Questions\Models\Question;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();

        // 1. كورساتي (المنشورة فقط)
        $myCourses = $user->courses()
            ->where('is_published', true)
            ->select('id', 'title')
            ->get();

        // 2. آخر 5 ملفات PDF فتحها الطالب (مع تجنب N+1)
        $recentPdfs = $user->pdfViews()
            ->with('pdf.lesson.chapter.course:id,title')
            ->latest('last_opened_at')
            ->limit(5)
            ->get()
            ->map(fn($view) => [
                'pdf_id' => $view->pdf_id,
                'title' => $view->pdf->title,
                'course_title' => $view->pdf->lesson->chapter->course->title ?? 'Unknown',
                'last_opened_at' => $view->last_opened_at?->toDateTimeString(),
            ]);

        // 3. إحصائيات بلاغاتي
        $totalReports = $user->questionReports()->count();
        $reportedQuestionIds = $user->questionReports()->pluck('question_id');
        
        $withExplanation = Question::whereIn('id', $reportedQuestionIds)
            ->whereHas('videos', function ($q) {
                $q->where('is_published', true);
            })
            ->count();
            
        $pending = max(0, $totalReports - $withExplanation);

        // 4. الفيديوهات المتاحة للأسئلة اللي بلغ عنها الطالب
        $availableVideos = $user->questionReports()
            ->with(['question.videos' => function ($q) {
                $q->where('is_published', true)->select('id', 'question_id', 'title', 'youtube_video_id');
            }])
            ->whereHas('question.videos', function ($q) {
                $q->where('is_published', true);
            })
            ->get()
            ->flatMap(fn($report) => $report->question->videos->map(fn($video) => [
                'video_id' => $video->id,
                'question_number' => $report->question->question_number,
                'title' => $video->title,
                'youtube_video_id' => $video->youtube_video_id,
                'embed_url' => "https://www.youtube.com/embed/{$video->youtube_video_id}"
            ]))->unique('video_id')->values();

        // 5. عدد الإشعارات غير المقروءة
        $unreadCount = $user->unreadNotifications()->count();

        return ApiResponse::success([
            'my_courses' => $myCourses,
            'recent_pdfs' => $recentPdfs,
            'my_reports_stats' => [
                'total' => $totalReports,
                'with_explanation' => $withExplanation,
                'pending' => $pending,
            ],
            'available_videos' => $availableVideos,
            'unread_notifications' => $unreadCount,
        ]);
    }
}