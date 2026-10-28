<?php

namespace App\Domain\Content\Observers;

use App\Domain\Content\Models\Course;
use App\Domain\Content\Queries\CourseTreeQuery;

class CourseObserver
{
    public function saved(Course $course): void
    {
        CourseTreeQuery::clearCache();
    }

    public function deleted(Course $course): void
    {
        CourseTreeQuery::clearCache();
    }
}