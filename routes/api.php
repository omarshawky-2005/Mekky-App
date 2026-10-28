<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\Auth\GoogleAuthController;
use App\Http\Controllers\Api\V1\Auth\OtpRegisterController;
use App\Http\Controllers\Api\V1\Auth\ProfileController;
use App\Http\Controllers\Api\V1\NotificationController; // تمت الإضافة لليوم 6
use App\Http\Controllers\Api\V1\Student\CourseController as StudentCourseController;
use App\Http\Controllers\Api\V1\Student\DashboardController as StudentDashboardController; // تمت الإضافة لليوم 6
use App\Http\Controllers\Api\V1\Student\ExplanationVideoController as StudentExplanationVideoController;
use App\Http\Controllers\Api\V1\Student\PdfController as StudentPdfController;
use App\Http\Controllers\Api\V1\Student\QuestionReportController as StudentQuestionReportController;
use App\Http\Controllers\Api\V1\Teacher\ChapterController;
use App\Http\Controllers\Api\V1\Teacher\CourseController as TeacherCourseController;
use App\Http\Controllers\Api\V1\Teacher\DashboardController as TeacherDashboardController; // تمت الإضافة لليوم 6
use App\Http\Controllers\Api\V1\Teacher\ExplanationVideoController as TeacherExplanationVideoController;
use App\Http\Controllers\Api\V1\Teacher\LessonController;
use App\Http\Controllers\Api\V1\Teacher\PdfController as TeacherPdfController;
use App\Http\Controllers\Api\V1\Teacher\QuestionDashboardController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes (V1)
|--------------------------------------------------------------------------
*/

Route::prefix('v1')->group(function () {
    
    // ==================== 1. Public Auth Routes ====================
    Route::post('/auth/register', [AuthController::class, 'register']);
    Route::post('/auth/login', [AuthController::class, 'login']);
    
    Route::post('/auth/register/step1', [OtpRegisterController::class, 'step1']);
    Route::post('/auth/register/step2', [OtpRegisterController::class, 'step2']);
    Route::post('/auth/register/step3', [OtpRegisterController::class, 'step3']);
    
    Route::post('/auth/google', [GoogleAuthController::class, 'login']);
    
    Route::post('/auth/forgot-password', [ProfileController::class, 'forgotPassword']);
    Route::post('/auth/reset-password', [ProfileController::class, 'resetPassword']);

    // ==================== 2. Protected Auth Routes ====================
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/auth/logout', [AuthController::class, 'logout']);
        Route::get('/auth/me', [AuthController::class, 'me']);
        
        Route::post('/auth/profile', [ProfileController::class, 'update']);
        Route::put('/auth/change-password', [ProfileController::class, 'changePassword']);
    });

    // ==================== 3. Student: Content Browsing ====================
    Route::get('/courses', [StudentCourseController::class, 'index']);
    Route::get('/courses/{id}', [StudentCourseController::class, 'show']);

    // ==================== 4. Student: PDFs & Question Reports ====================
    Route::middleware(['auth:sanctum', 'student'])->group(function () {
        Route::get('/pdfs/{pdf}/file', [StudentPdfController::class, 'showFile']);
        Route::post('/pdfs/{pdf}/view', [StudentPdfController::class, 'trackView']);
        
        Route::get('/me/question-reports', [StudentQuestionReportController::class, 'index']);
        Route::post('/pdfs/{pdf}/question-reports', [StudentQuestionReportController::class, 'store']);
    });

    // ==================== 5. Student: Videos & Dashboard (Day 6) ====================
    Route::middleware(['auth:sanctum', 'student'])->group(function () {
        // Videos (Simplified for YouTube)
        Route::get('/questions/{question}/videos', [StudentExplanationVideoController::class, 'index']);
        Route::get('/videos/{video}', [StudentExplanationVideoController::class, 'show']);
        
        // Student Dashboard
        Route::get('/me/dashboard', [StudentDashboardController::class, 'index']);
    });

    // ==================== 6. Notifications (Day 6) ====================
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/notifications', [NotificationController::class, 'index']);
        Route::get('/notifications/unread-count', [NotificationController::class, 'unreadCount']);
        Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead']);
        Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead']);
    });

    // ==================== 7. Teacher: Content, Dashboard & Videos ====================
    Route::middleware(['auth:sanctum', 'teacher'])->prefix('teacher')->group(function () {
        
        // --- Courses ---
        Route::get('/courses', [TeacherCourseController::class, 'index']);
        Route::post('/courses', [TeacherCourseController::class, 'store']);
        Route::get('/courses/{course}', [TeacherCourseController::class, 'show']);
        Route::put('/courses/{course}', [TeacherCourseController::class, 'update']);
        Route::delete('/courses/{course}', [TeacherCourseController::class, 'destroy']);
        Route::post('/courses/{course}/publish', [TeacherCourseController::class, 'publish']);
        Route::post('/courses/{course}/unpublish', [TeacherCourseController::class, 'unpublish']);

        // --- Chapters ---
        Route::post('/courses/{course}/chapters', [ChapterController::class, 'store']);
        Route::put('/chapters/{chapter}', [ChapterController::class, 'update']);
        Route::delete('/chapters/{chapter}', [ChapterController::class, 'destroy']);

        // --- Lessons ---
        Route::post('/chapters/{chapter}/lessons', [LessonController::class, 'store']);
        Route::put('/lessons/{lesson}', [LessonController::class, 'update']);
        Route::delete('/lessons/{lesson}', [LessonController::class, 'destroy']);
        Route::patch('/chapters/{chapter}/lessons/reorder', [LessonController::class, 'reorder']);

        // --- PDFs ---
        Route::post('/lessons/{lesson}/pdfs', [TeacherPdfController::class, 'store']);
        Route::delete('/pdfs/{pdf}', [TeacherPdfController::class, 'destroy']);

        // --- Question Dashboard ---
        Route::get('/questions', [QuestionDashboardController::class, 'index']);
        Route::get('/questions/{question}', [QuestionDashboardController::class, 'show']);

        // --- Teacher Dashboard (Day 6) ---
        Route::get('/dashboard/stats', [TeacherDashboardController::class, 'stats']);

        // --- Videos (Simplified for YouTube) ---
        Route::post('/questions/{question}/videos', [TeacherExplanationVideoController::class, 'store']);
        Route::put('/videos/{video}', [TeacherExplanationVideoController::class, 'update']);
        Route::post('/videos/{video}/publish', [TeacherExplanationVideoController::class, 'publish']); // <-- تمت الإضافة (مهمة جداً للإشعارات)
        Route::delete('/videos/{video}', [TeacherExplanationVideoController::class, 'destroy']);
        // (تم حذف route الـ uploaded لأنه لم يعد مطلوباً مع YouTube)
    });

});