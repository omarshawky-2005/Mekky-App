<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserIsTeacher
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json(['message' => 'غير مسجل دخول'], 401);
        }
        
        if ($user->role !== 'teacher' && $user->role?->value !== 'teacher') {
            return response()->json(['message' => 'ليس معلم'], 403);
        }
        
        return $next($request);
    }
}