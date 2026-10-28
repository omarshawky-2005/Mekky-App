<?php

namespace App\Http\Resources\Questions;

use App\Http\Resources\Content\PdfResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionWithReportsResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question_number' => $this->question_number,
            'reports_count' => $this->reports_count,
            'last_reported_at' => $this->last_reported_at?->toDateTimeString(),
            
            // ✅ التصحيح هنا: استخدام videos() بدلاً من explanationVideos()
            'has_explanation' => $this->videos()->where('is_published', true)->exists(),
            
            'pdf' => new PdfResource($this->whenLoaded('pdf')),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}