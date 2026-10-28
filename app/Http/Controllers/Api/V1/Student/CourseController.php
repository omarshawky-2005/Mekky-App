<?php

namespace App\Http\Controllers\Api\V1\Student;

use App\Domain\Content\Queries\CourseTreeQuery;
use App\Http\Controllers\Controller;
use App\Http\Resources\Content\CourseResource;
use App\Http\Resources\Content\CourseTreeResource;
use App\Support\ApiResponse;
use Illuminate\Http\JsonResponse;

class CourseController extends Controller
{
    protected CourseTreeQuery $query;

    public function __construct(CourseTreeQuery $query)
    {
        $this->query = $query;
    }

    /**
     * عرض قائمة الكورسات المنشورة (مع Pagination).
     */
    public function index(): JsonResponse
    {
        $courses = $this->query->getPublishedCourses();

        return ApiResponse::success(CourseResource::collection($courses));
    }

    /**
     * عرض شجرة الكورس الواحد (كورس -> فصول -> دروس).
     */
    public function show(int $id): JsonResponse
    {
        $course = $this->query->getCourseTree($id);

        if (!$course) {
            return ApiResponse::error('Course not found or not published.', 404);
        }

        return ApiResponse::success(new CourseTreeResource($course));
    }
}