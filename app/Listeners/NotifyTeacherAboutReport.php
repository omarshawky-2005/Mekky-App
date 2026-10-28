<?php

namespace App\Listeners;

use App\Events\NewReportEvent;
use App\Notifications\NewReportNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

class NotifyTeacherAboutReport implements ShouldQueue
{
    public function handle(NewReportEvent $event): void
    {
        $report = $event->report;
        
        // جلب المدرس صاحب الكورس
        $teacher = $report->question->pdf->lesson->chapter->course->teacher;
        
        if ($teacher) {
            $teacher->notify(new NewReportNotification($report));
        }
    }
}