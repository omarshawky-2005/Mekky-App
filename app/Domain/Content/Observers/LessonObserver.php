<?php

namespace App\Domain\Content\Observers;

use App\Domain\Content\Models\Lesson;
use App\Domain\Content\Queries\CourseTreeQuery;

class LessonObserver
{
    public function saved(Lesson $lesson): void
    {
        CourseTreeQuery::clearCache();
    }

    public function deleted(Lesson $lesson): void
    {
        CourseTreeQuery::clearCache();
    }
}