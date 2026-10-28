<?php
namespace App\Http\Requests\Auth;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegisterStep2Request extends FormRequest
{
    public function authorize(): bool { return true; }
    public function rules(): array
    {
        return [
            'registration_token' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }
}