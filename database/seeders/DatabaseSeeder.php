<?php

namespace Database\Seeders;

use App\Domain\Auth\Models\User;
use App\Domain\Content\Models\Course;
use App\Domain\Content\Models\Chapter;
use App\Domain\Content\Models\Lesson;
use App\Domain\Content\Models\Pdf;
use App\Domain\Questions\Models\Question;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. إنشاء المدرس الأساسي
        $teacher = User::factory()->teacher()->create([
            'name' => 'mekky',
            'email' => 'mekky@test.com',
        ]);

        // 2. إنشاء الطالب الأساسي
        $student = User::factory()->student()->create([
            'name' => 'محمداحمد',
            'email' => 'student@gmail.com',
        ]);

        // 3. إنشاء كورس منشور
        $course = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'title' => 'الرياضيات للصف الثالث الثانوي',
            'is_published' => true,
        ]);

        // 4. إنشاء فصول ودروس وملفات PDF وأسئلة
        for ($i = 1; $i <= 3; $i++) {
            $chapter = Chapter::factory()->create([
                'course_id' => $course->id,
                'title' => 'الفصل ' . $i,
                'position' => $i,
            ]);

            for ($j = 1; $j <= 2; $j++) {
                $lesson = Lesson::factory()->create([
                    'chapter_id' => $chapter->id,
                    'title' => 'الدرس ' . $j,
                    'position' => $j,
                ]);

                $pdf = Pdf::factory()->create([
                    'lesson_id' => $lesson->id,
                    'title' => 'ملف الدرس ' . $j,
                ]);

                // إنشاء بعض الأسئلة لكل PDF
                Question::factory()->count(5)->create([
                    'pdf_id' => $pdf->id,
                ]);
            }
        }
    }
}