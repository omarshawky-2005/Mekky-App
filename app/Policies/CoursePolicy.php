<?php

namespace App\Policies;

use App\Domain\Auth\Models\User;
use App\Domain\Content\Models\Course;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        return true; // أي حد يقدر يشوف الكورس
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === \App\Enums\UserRole::Teacher;
    }

    /**
     * Determine whether the user can update the model.
     */
     public function update(User $user, Course $course): bool
    {
        // تأكد أن المستخدم هو من أنشأ الكورس
        return $user->id === $course->teacher_id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        // المدرس يقدر يحذف الكورس بتاعه فقط
        return $user->role === \App\Enums\UserRole::Teacher && $user->id === $course->teacher_id;
    }
}