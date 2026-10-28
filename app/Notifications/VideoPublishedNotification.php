<?php

namespace App\Notifications;

use App\Domain\Videos\Models\ExplanationVideo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class VideoPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected ExplanationVideo $video;

    public function __construct(ExplanationVideo $video)
    {
        $this->video = $video;
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toArray($notifiable): array
    {
        return [
            'type' => 'video_published',
            'video_id' => $this->video->id,
            'question_id' => $this->video->question_id,
            'title' => $this->video->title,
            'message' => "تم نشر فيديو شرح جديد للسؤال رقم {$this->video->question->question_number}",
        ];
    }
}