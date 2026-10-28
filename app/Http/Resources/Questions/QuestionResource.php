<?php

namespace App\Http\Resources\Questions;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'pdf_id' => $this->pdf_id,
            'question_number' => $this->question_number,
            'reports_count' => $this->reports_count,
            'last_reported_at' => $this->last_reported_at?->toDateTimeString(),
            'has_explanation' => $this->explanationVideos()->where('is_published', true)->exists(),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}