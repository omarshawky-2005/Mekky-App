<?php

namespace App\Listeners;

use App\Domain\Questions\Models\QuestionReport;
use App\Domain\Videos\Models\ExplanationVideo;
use App\Events\VideoPublishedEvent;
use App\Notifications\VideoPublishedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyStudentsAboutVideo implements ShouldQueue
{
    public function handle(VideoPublishedEvent $event): void
    {
        $video = $event->video;
        
        // جلب الطلاب اللي بلغوا عن السؤال ده
        $studentIds = QuestionReport::where('question_id', $video->question_id)
            ->distinct()
            ->pluck('student_id');
        
        // إرسال الإشعار على دفعات (chunks) عشان ما نحمّلش الذاكرة
        $studentIds->chunk(100)->each(function ($chunk) use ($video) {
            foreach ($chunk as $studentId) {
                $student = \App\Domain\Auth\Models\User::find($studentId);
                if ($student) {
                    $student->notify(new VideoPublishedNotification($video));
                }
            }
        });
    }
}