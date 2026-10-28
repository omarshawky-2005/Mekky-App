<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
    $middleware->alias([
        'teacher' => \App\Http\Middleware\EnsureUserIsTeacher::class,
        'student' => \App\Http\Middleware\EnsureUserIsStudent::class,
    ]);

    $middleware->throttleApi(60, 1); // 60 طلب في الدقيقة لكل مستخدم
})
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
