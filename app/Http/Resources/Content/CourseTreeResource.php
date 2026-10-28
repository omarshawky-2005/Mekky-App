<?php

namespace App\Http\Resources\Content;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseTreeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'is_published' => (bool) $this->is_published,
            'teacher_name' => $this->teacher?->name,
            'chapters' => ChapterResource::collection($this->whenLoaded('chapters', function () {
                // بنحمل الدروس جوه كل فصل عشان نتجنب N+1
                return $this->chapters->map(function ($chapter) {
                    $chapter->setRelation('lessons', $chapter->lessons);
                    return $chapter;
                });
            })),
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}