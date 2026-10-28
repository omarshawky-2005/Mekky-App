<?php

namespace App\Http\Requests\Teacher;

use App\Enums\UserRole;
use Illuminate\Foundation\Http\FormRequest;

class StorePdfRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->user();
        
        // المقارنة الصحيحة: كائن Enum مع كائن Enum
        return $user && $user->role === UserRole::Teacher;
    }

        public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:150'],
            // تمت إضافة mimetypes للتأكد من أن الملف PDF فعلياً وليس مجرد تغيير امتداد
            'file' => ['required', 'file', 'mimetypes:application/pdf', 'mimes:pdf', 'max:20480'], 
            'existing_pdf_id' => ['nullable', 'exists:pdfs,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'file.mimes' => 'الملف يجب أن يكون PDF فقط.',
            'file.max' => 'حجم الملف يجب أن لا يتجاوز 20 ميجابايت.',
        ];
    }
}