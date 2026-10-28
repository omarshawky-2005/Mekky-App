<?php

namespace App\Http\Requests\Teacher;

use App\Enums\AccessType;
use Illuminate\Foundation\Http\FormRequest;

class StoreExplanationVideoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->role === \App\Enums\UserRole::Teacher;
    }

    public function rules(): array
    {
        return [
           'title' => ['required', 'string', 'max:150'],
           'external_id' => ['required', 'string', 'max:50'], // معرف فيديو اليوتيوب
        ];
    }

    public function after(): array
    {
        return [
            function () {
                if ($this->input('access_type') === 'paid' && !$this->input('price')) {
                    $this->validator->errors()->add(
                        'price',
                        'السعر مطلوب عندما يكون الفيديو مدفوعاً'
                    );
                }
            },
        ];
    }
}