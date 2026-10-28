<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Domain\Questions\Models\Question;
use App\Domain\Videos\Models\ExplanationVideo;
use App\Http\Controllers\Controller;
use App\Http\Resources\Videos\ExplanationVideoResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class ExplanationVideoController extends Controller
{
    /**
     * عرض الفيديوهات الخاصة بسؤال معين
     */
    public function index(Question $question): JsonResponse
    {
        // جلب الفيديوهات المنشورة فقط لهذا السؤال
        $videos = ExplanationVideo::where('question_id', $question->id)
            ->where('is_published', true)
            ->orderBy('created_at', 'desc')
            ->get();

        return ApiResponse::success(
            ExplanationVideoResource::collection($videos),
            'تم جلب الفيديوهات بنجاح'
        );
    }

    /**
     * عرض تفاصيل فيديو معين (اختياري، حسب احتياج الـ Routes)
     */
    public function show(ExplanationVideo $video): JsonResponse
    {
        // التأكد من أن الفيديو منشور
        if (!$video->is_published) {
            return ApiResponse::error('هذا الفيديو غير متاح حالياً', 404);
        }

        return ApiResponse::success(
            new ExplanationVideoResource($video),
            'تم جلب الفيديو بنجاح'
        );
    }
}