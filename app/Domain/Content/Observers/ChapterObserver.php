<?php

namespace App\Domain\Content\Observers;

use App\Domain\Content\Models\Chapter;
use App\Domain\Content\Queries\CourseTreeQuery;

class ChapterObserver
{
    public function saved(Chapter $chapter): void
    {
        CourseTreeQuery::clearCache();
    }

    public function deleted(Chapter $chapter): void
    {
        CourseTreeQuery::clearCache();
    }
}