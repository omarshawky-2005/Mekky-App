<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UpdateProfileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // أي مستخدم مسجل يقدر يعدل ملفه
    }

    public function rules(): array
    {
        return [
            'name' => ['sometimes', 'string', 'max:100'],
            'phone' => ['sometimes', 'string', 'max:20'],
            'subject' => ['sometimes', 'nullable', 'string', 'max:100'],
            'telegram_link' => ['sometimes', 'nullable', 'url', 'max:255'],
            'avatar' => ['sometimes', 'nullable', File::image()->max(2048)], // 2MB Max
        ];
    }
} 