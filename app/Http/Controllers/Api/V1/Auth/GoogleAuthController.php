<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Domain\Auth\Models\User;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class GoogleAuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'google_token' => ['required', 'string'],
        ]);

        // التحقق من الـ Token مع خوادم جوجل
        $response = Http::get('https://oauth2.googleapis.com/tokeninfo?id_token=' . $request->google_token);

        if ($response->failed() || !isset($response['email'])) {
            return ApiResponse::error('Google token is invalid or expired', 401);
        }

        $googleUser = $response->json();

        // البحث عن المستخدم أو إنشاؤه
        $user = User::firstOrCreate(
            ['email' => $googleUser['email']],
            [
                'name' => $googleUser['name'] ?? 'Google User',
                'password' => bcrypt(str()->random(16)), // باسورد عشوائي لأنه يسجل بجوجل
                'role' => UserRole::Student,
                'avatar' => $googleUser['picture'] ?? null,
            ]
        );

        // إنشاء Token للمستخدم
        $token = $user->createToken('google_auth_token')->plainTextToken;

        return ApiResponse::success([
            'user' => $user,
            'token' => $token,
        ], 'تم تسجيل الدخول بنجاح عبر جوجل');
    }
}