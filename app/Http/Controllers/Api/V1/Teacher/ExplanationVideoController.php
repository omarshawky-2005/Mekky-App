<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Domain\Questions\Models\Question;
use App\Domain\Videos\Models\ExplanationVideo;
use App\Events\VideoPublishedEvent; // <-- تمت الإضافة
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreExplanationVideoRequest;
use App\Http\Resources\Videos\ExplanationVideoResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ExplanationVideoController extends Controller
{
        public function store(StoreExplanationVideoRequest $request, Question $question): JsonResponse
    {
        $video = ExplanationVideo::create([
            'question_id' => $question->id,
            'teacher_id' => $request->user()->id,
            'title' => $request->title,
            'external_id' => $request->external_id,
            'is_published' => false,
        ]);

        return ApiResponse::success(new ExplanationVideoResource($video), 'تم إضافة الفيديو بنجاح', 201);
    }

    public function update(StoreExplanationVideoRequest $request, ExplanationVideo $video): JsonResponse
    {
        if ($video->teacher_id !== $request->user()->id) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $video->update($request->validated());
        return ApiResponse::success(new ExplanationVideoResource($video), 'تم تحديث الفيديو');
    }

    public function destroy(ExplanationVideo $video): JsonResponse
    {
        if ($video->teacher_id !== request()->user()->id) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $video->delete();
        return ApiResponse::success(null, 'تم حذف الفيديو');
    }

    // <-- الدالة الجديدة لنشر الفيديو وإشعار الطلاب
    public function publish(ExplanationVideo $video): JsonResponse
    {
        if ($video->teacher_id !== request()->user()->id) {
            return ApiResponse::error('Unauthorized', 403);
        }

        $video->update(['is_published' => true]);
        
        // <-- إطلاق الحدث لإشعار الطلاب اللي بلغوا عن السؤال ده
        event(new VideoPublishedEvent($video));

        return ApiResponse::success(null, 'تم نشر الفيديو وإشعار الطلاب');
    }
}