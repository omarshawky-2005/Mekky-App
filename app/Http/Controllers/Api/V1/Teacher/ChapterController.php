<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Domain\Content\Models\Chapter;
use App\Domain\Content\Models\Course;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreChapterRequest;
use App\Http\Requests\Teacher\UpdateChapterRequest;
use App\Http\Resources\Content\ChapterResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ChapterController extends Controller
{
    public function store(StoreChapterRequest $request, Course $course): JsonResponse
    {
        Gate::authorize('update', $course);

        $chapter = $course->chapters()->create($request->validated());

        return ApiResponse::success(new ChapterResource($chapter), 'تم إنشاء الفصل بنجاح', 201);
    }

    public function update(UpdateChapterRequest $request, Chapter $chapter): JsonResponse
    {
        Gate::authorize('update', $chapter->course);

        $chapter->update($request->validated());

        return ApiResponse::success(new ChapterResource($chapter), 'تم تحديث الفصل بنجاح');
    }

    public function destroy(Chapter $chapter): JsonResponse
    {
        Gate::authorize('delete', $chapter->course);

        $chapter->delete();

        return ApiResponse::success(null, 'تم حذف الفصل بنجاح');
    }
}