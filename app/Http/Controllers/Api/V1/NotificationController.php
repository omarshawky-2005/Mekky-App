<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Support\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $notifications = $request->user()->notifications()->paginate(15);
        return ApiResponse::success($notifications);
    }

    public function unreadCount(Request $request)
    {
        $userId = $request->user()->id;
        $cacheKey = "user:{$userId}:unread_notifications_count";
        
        // كاش لمدة 10 دقائق (600 ثانية)
        $count = Cache::remember($cacheKey, 600, function () use ($userId) {
            return \App\Domain\Auth\Models\User::find($userId)->unreadNotifications()->count();
        });

        return ApiResponse::success(['count' => $count]);
    }

    public function markAsRead(Request $request, string $id)
    {
        $notification = $request->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        $this->clearUnreadCache($request->user()->id);
        return ApiResponse::success(null, 'تم تعليم الإشعار كمقروء');
    }

    public function markAllAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();
        
        $this->clearUnreadCache($request->user()->id);
        return ApiResponse::success(null, 'تم تعليم جميع الإشعارات كمقروءة');
    }

    private function clearUnreadCache(int $userId): void
    {
        Cache::forget("user:{$userId}:unread_notifications_count");
    }
}