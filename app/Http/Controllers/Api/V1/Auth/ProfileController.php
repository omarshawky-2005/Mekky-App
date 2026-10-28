<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Domain\Auth\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\UpdateProfileRequest;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ProfileController extends Controller
{
    /**
     * تحديث بيانات الملف الشخصي (بما في ذلك الصورة)
     */
    public function update(UpdateProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // التعامل مع رفع الصورة لو موجودة
        if ($request->hasFile('avatar')) {
            // حذف الصورة القديمة لو موجودة
            if ($user->avatar) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($user->avatar);
            }
            // حفظ الصورة الجديدة
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->update($data);

        return ApiResponse::success($user, 'تم تحديث الملف الشخصي بنجاح');
    }

    /**
     * تغيير كلمة المرور الحالية
     */
    public function changePassword(Request $request): JsonResponse
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return ApiResponse::error('كلمة المرور الحالية غير صحيحة', 400);
        }

        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return ApiResponse::success(null, 'تم تغيير كلمة المرور بنجاح');
    }

    /**
     * طلب إعادة تعيين كلمة المرور (إرسال OTP)
     */
    public function forgotPassword(Request $request): JsonResponse
{
    $request->validate(['email' => ['required', 'email']]);

    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return ApiResponse::success(null, 'إذا كان هذا البريد الإلكتروني مسجلاً، فسيتم إرسال رمز التحقق إليه.');
    }

    $otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);

    DB::table('password_reset_tokens')->updateOrInsert(
        ['email' => $request->email],
        ['token' => $otp, 'created_at' => now()]
    );

    // إرسال الإيميل فعلياً
    \Illuminate\Support\Facades\Mail::to($request->email)->send(
        new \App\Mail\OtpMail($otp, 'إعادة تعيين كلمة المرور')
    );

    return ApiResponse::success(null, 'تم إرسال رمز التحقق إلى بريدك الإلكتروني');
}

    /**
     * إعادة تعيين كلمة المرور باستخدام OTP
     */
    public function resetPassword(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
            'otp' => ['required', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $resetRecord = DB::table('password_reset_tokens')->where('email', $request->email)->first();

        if (!$resetRecord || $resetRecord->token !== $request->otp) {
            return ApiResponse::error('رمز التحقق غير صحيح', 400);
        }

        // التحقق من صلاحية الـ OTP (ساعة واحدة مثلاً)
        if (Carbon::parse($resetRecord->created_at)->addHour()->isPast()) {
            return ApiResponse::error('انتهت صلاحية رمز التحقق', 400);
        }

        $user = User::where('email', $request->email)->first();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        // حذف الـ OTP بعد الاستخدام الناجح
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return ApiResponse::success(null, 'تم إعادة تعيين كلمة المرور بنجاح');
    }
}