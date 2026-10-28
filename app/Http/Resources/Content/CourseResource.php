<?php

namespace App\Http\Resources\Content;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CourseResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'is_published' => (bool) $this->is_published,
            'teacher_name' => $this->teacher?->name,
            'created_at' => $this->created_at->toDateTimeString(),
        ];
    }
}