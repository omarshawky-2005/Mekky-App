<?php

namespace App\Http\Requests\Student;

use Illuminate\Foundation\Http\FormRequest;

class ReportQuestionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === \App\Enums\UserRole::Student;
    }

    public function rules(): array
    {
        return [
            'question_number' => ['required', 'integer', 'min:1'],
            'question_text' => ['nullable', 'string', 'max:500'],
        ];
    }
}