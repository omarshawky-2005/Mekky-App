<?php

namespace App\Notifications;

use App\Domain\Questions\Models\QuestionReport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NewReportNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected QuestionReport $report;

    public function __construct(QuestionReport $report)
    {
        $this->report = $report;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'new_report',
            'report_id' => $this->report->id,
            'question_id' => $this->report->question_id,
            'question_number' => $this->report->question->question_number,
            'message' => "طالب بلّغ عن السؤال رقم {$this->report->question->question_number}",
        ];
    }
}