<?php

namespace App\Events;

use App\Domain\Videos\Models\ExplanationVideo;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VideoPublishedEvent
{
    use Dispatchable, SerializesModels;

    public ExplanationVideo $video;

    public function __construct(ExplanationVideo $video)
    {
        $this->video = $video;
    }
}