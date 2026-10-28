<?php

namespace App\Domain\Content\Queries;

use App\Domain\Content\Models\Course;
use Illuminate\Support\Facades\Cache;

class CourseTreeQuery
{
    public function getCourseTree(int $courseId): ?Course
    {
        $cacheKey = "course_tree_{$courseId}";

        // بنستخدم remember، لكن لو النتيجة null هنشيلها من الكاش فوراً
        $course = Cache::tags(['courses'])->remember($cacheKey, 3600, function () use ($courseId) {
            return Course::with(['chapters.lessons'])
                ->where('is_published', true)
                ->find($courseId);
        });

        // لو الكورس مش موجود، نمسحه من الكاش عشان ما يتخزنش
        if (!$course) {
            Cache::tags(['courses'])->forget($cacheKey);
        }

        return $course;
    }

    /**
     * Get all published courses, cached.
     */
    public function getPublishedCourses()
    {
        $cacheKey = "published_courses_list";

        return Cache::tags(['courses'])->remember($cacheKey, 3600, function () {
            return Course::where('is_published', true)
                ->latest()
                ->paginate(15);
        });
    }

    /**
     * Clear the course tree cache.
     */
    public static function clearCache(): void
    {
        Cache::tags(['courses'])->flush();
    }
}