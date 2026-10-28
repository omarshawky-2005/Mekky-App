<?php

namespace App\Http\Requests\Teacher;

use Illuminate\Foundation\Http\FormRequest;

class StoreChapterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === \App\Enums\UserRole::Teacher;
    }

    public function rules(): array
    {
        return [
            
            'title' => ['required', 'string', 'max:100'],
            'position' => ['required', 'integer', 'min:1'],
        ];
    }
}