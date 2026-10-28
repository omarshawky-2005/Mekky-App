<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Domain\Auth\Models\User;
use App\Enums\UserRole;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterStep1Request;
use App\Http\Requests\Auth\RegisterStep2Request;
use App\Http\Requests\Auth\RegisterStep3Request;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class OtpRegisterController extends Controller
{
    private string $cachePrefix = 'register_step_';

    /**
     * الخطوة 1: استقبال البيانات الأساسية وتوليد Token و OTP
     */
    public function step1(RegisterStep1Request $request): JsonResponse
{
    $regToken = Str::random(32);
    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    Cache::put($this->cachePrefix . $regToken, [
        'name' => $request->name,
        'email' => $request->email,
        'otp' => $otp,
    ], now()->addMinutes(10));

    // إرسال الإيميل (لو Mailtrap مضبوط)
    // Mail::to($request->email)->send(new OtpMail($otp));

    return ApiResponse::success([
        'registration_token' => $regToken,
        'otp' => app()->environment('local') ? $otp : null, // يُرجع OTP في التطوير فقط
    ], 'تم إرسال رمز التحقق إلى بريدك الإلكتروني', 200);
}

    /**
     * الخطوة 2: استقبال كلمة المرور
     */
    public function step2(RegisterStep2Request $request): JsonResponse
    {
        $cacheKey = $this->cachePrefix . $request->registration_token;
        $data = Cache::get($cacheKey);

        if (!$data) {
            return ApiResponse::error('انتهت صلاحية جلسة التسجيل أو الرمز غير صحيح', 400);
        }

        // تحديث البيانات المؤقتة بكلمة المرور
        $data['password'] = Hash::make($request->password);
        Cache::put($cacheKey, $data, now()->addMinutes(10));

        return ApiResponse::success(null, 'تم حفظ كلمة المرور، يرجى تأكيد رمز التحقق');
    }

    /**
     * الخطوة 3: تأكيد OTP وإنشاء الحساب
     */
    public function step3(RegisterStep3Request $request): JsonResponse
    {
        $cacheKey = $this->cachePrefix . $request->registration_token;
        $data = Cache::get($cacheKey);

        if (!$data) {
            return ApiResponse::error('انتهت صلاحية جلسة التسجيل أو الرمز غير صحيح', 400);
        }

        if ($data['otp'] !== $request->otp) {
            return ApiResponse::error('رمز التحقق غير صحيح', 400);
        }

        // إنشاء المستخدم النهائي
        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => UserRole::Student,
        ]);

        // مسح البيانات المؤقتة
        Cache::forget($cacheKey);

        $token = $user->createToken('auth_token')->plainTextToken;

        return ApiResponse::success([
            'user' => $user,
            'token' => $token,
        ], 'تم التسجيل بنجاح', 201);
    }
}