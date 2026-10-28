<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class ReorderLessonsRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === \App\Enums\UserRole::Teacher;
    }

    public function rules(): array
    {
        return [
            'lessons' => ['required', 'array', 'min:1'],
            'lessons.*.id' => ['required', 'exists:lessons,id'],
            'lessons.*.position' => ['required', 'integer', 'min:1'],
        ];
    }
}