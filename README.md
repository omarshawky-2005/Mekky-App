# 🎓 EduPlatform API (Backend)

منصة تعليمية متكاملة لإدارة الكورسات، ملفات الـ PDF، نظام بلاغات الأسئلة الصعبة، وفيديوهات الشرح المدمجة (YouTube).

## 🚀 المميزات
- ✅ **نظام مصادقة آمن**: Laravel Sanctum (تسجيل بخطوات OTP + تسجيل دخول تقليدي + Google Auth).
- ✅ **إدارة محتوى هرمية**: Courses → Chapters → Lessons → PDFs مع ترتيب ديناميكي.
- ✅ **نظام بلاغات ذكي**: تجميع تلقائي للبلاغات تحت نفس السؤال مع منع التكرار (Idempotency) وحماية من الـ Race Conditions.
- ✅ **دمج فيديوهات YouTube**: نظام نشر فيديوهات مع إطلاق إشعارات فورية للطلاب الذين بلغوا عن السؤال.
- ✅ **لوحات تحكم (Dashboards)**: إحصائيات شاملة للطالب والمدرس مع استخدام Caching محسّن (Redis) لمنع الـ N+1 Queries.
- ✅ **بنية Clean Code**: مقسمة حسب الـ Domain (Actions, Services, Query Classes, Form Requests, API Resources).

## 🛠️ متطلبات التشغيل
- PHP 8.3 أو أحدث
- Laravel 11
- MySQL 8.0+
- Redis (للكاش، الـ Sessions، والـ Queues)
- Composer

## ⚙️ خطوات التثبيت المحلي

1. استنساخ المشروع وتثبيت الحزم:
   ```bash
   git clone <repository_url>
   cd <project_folder>
   composer install