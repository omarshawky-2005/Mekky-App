<?php

namespace App\Http\Resources\Videos;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExplanationVideoResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'question_id' => $this->question_id,
            'teacher_id' => $this->teacher_id,
            'title' => $this->title,
            'external_id' => $this->external_id,
            'is_published' => (bool) $this->is_published,
            'embed_url' => "https://www.youtube.com/embed/{$this->youtube_video_id}",
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            
            // بيانات إضافية حسب نوع الوصول
           // 'watch_url' => $this->when(
                //$this->canWatch($request->user()),
                //fn() => route('videos.playlist', $this->id)
            //),
        ];
    }

    private function canWatch($user): bool
    {
        // تحقق من أن المستخدم مسجل الدخول وأنه معلم أو مشترك في الدورة
        if (!$user) {
            return false;
        }

        // إذا كان المستخدم هو المعلم الذي أضاف الفيديو، يمكنه المشاهدة
        if ($user->id === $this->teacher_id) {
            return true;
        }

        // تحقق من أن المستخدم مشترك في الدورة التي تحتوي على السؤال
        return $user->isSubscribedToQuestion($this->question_id);
    }
}