<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Domain\Content\Models\Chapter;
use App\Domain\Content\Models\Lesson;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\ReorderLessonsRequest;
use App\Http\Requests\Teacher\StoreLessonRequest;
use App\Http\Requests\Teacher\UpdateLessonRequest;
use App\Http\Resources\Content\LessonResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class LessonController extends Controller
{
    public function store(StoreLessonRequest $request, Chapter $chapter): JsonResponse
    {
        Gate::authorize('update', $chapter->course);

        $lesson = $chapter->lessons()->create($request->validated());

        return ApiResponse::success(new LessonResource($lesson), 'تم إنشاء الدرس بنجاح', 201);
    }

    public function update(UpdateLessonRequest $request, Lesson $lesson): JsonResponse
    {
        Gate::authorize('update', $lesson->chapter->course);

        $lesson->update($request->validated());

        return ApiResponse::success(new LessonResource($lesson), 'تم تحديث الدرس بنجاح');
    }

    public function destroy(Lesson $lesson): JsonResponse
    {
        Gate::authorize('delete', $lesson->chapter->course);

        $lesson->delete();

        return ApiResponse::success(null, 'تم حذف الدرس بنجاح');
    }

    public function reorder(ReorderLessonsRequest $request, Chapter $chapter): JsonResponse
    {
        Gate::authorize('update', $chapter->course);

        // تنفيذ إعادة الترتيب داخل Transaction لضمان سلامة البيانات
        DB::transaction(function () use ($request, $chapter) {
            foreach ($request->input('lessons') as $lessonData) {
                $chapter->lessons()->where('id', $lessonData['id'])->update([
                    'position' => $lessonData['position']
                ]);
            }
        });

        return ApiResponse::success(null, 'تم إعادة ترتيب الدروس بنجاح');
    }
}