<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, sans-serif; background: #f4f4f4; padding: 20px; }
        .container { max-width: 500px; margin: 0 auto; background: #fff; padding: 30px; border-radius: 10px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .otp { font-size: 32px; font-weight: bold; color: #2563eb; text-align: center; padding: 15px; background: #f0f7ff; border-radius: 8px; margin: 20px 0; letter-spacing: 5px; }
        .warning { color: #dc2626; font-size: 14px; margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container">
        <h2>مرحباً بك في EduPlatform 👋</h2>
        <p>رمز التحقق الخاص بك هو:</p>
        <div class="otp">{{ $otp }}</div>
        <p>الغرض: {{ $purpose }}</p>
        <p>هذا الرمز صالح لمدة <strong>10 دقائق</strong> فقط.</p>
        <p class="warning">⚠️ لا تشارك هذا الرمز مع أي شخص.</p>
        <hr>
        <p style="color: #888; font-size: 12px;">إذا لم تطلب هذا الرمز، يرجى تجاهل هذه الرسالة.</p>
    </div>
</body>
</html>