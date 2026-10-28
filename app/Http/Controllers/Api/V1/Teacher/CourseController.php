<?php

namespace App\Http\Controllers\Api\V1\Teacher;

use App\Domain\Content\Models\Course;
use App\Http\Controllers\Controller;
use App\Http\Requests\Teacher\StoreCourseRequest;
use App\Http\Requests\Teacher\UpdateCourseRequest;
use App\Http\Resources\Content\CourseResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class CourseController extends Controller
{
    public function index(): JsonResponse
    {
        $courses = auth()->user()->courses()->latest()->get();
        
        return ApiResponse::success(CourseResource::collection($courses));
    }

    public function store(StoreCourseRequest $request): JsonResponse
    {
        $course = auth()->user()->courses()->create($request->validated());

        return ApiResponse::success(new CourseResource($course), 'تم إنشاء الكورس بنجاح', 201);
    }

    public function show(Course $course): JsonResponse
    {
        Gate::authorize('view', $course);

        return ApiResponse::success(new CourseResource($course));
    }

    public function update(UpdateCourseRequest $request, Course $course): JsonResponse
    {
        Gate::authorize('update', $course);

        $course->update($request->validated());

        return ApiResponse::success(new CourseResource($course), 'تم تحديث الكورس بنجاح');
    }

    public function destroy(Course $course): JsonResponse
    {
        Gate::authorize('delete', $course);

        $course->delete();

        return ApiResponse::success(null, 'تم حذف الكورس بنجاح');
    }

    public function publish(Course $course): JsonResponse
    {
        Gate::authorize('update', $course);

        $course->update(['is_published' => true]);

        return ApiResponse::success(new CourseResource($course), 'تم نشر الكورس بنجاح');
    }

    public function unpublish(Course $course): JsonResponse
    {
        Gate::authorize('update', $course);

        $course->update(['is_published' => false]);

        return ApiResponse::success(new CourseResource($course), 'تم إلغاء نشر الكورس بنجاح');
    }
}