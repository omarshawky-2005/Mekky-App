<?php

namespace Database\Factories;

use App\Domain\Content\Models\Lesson;
use Illuminate\Database\Eloquent\Factories\Factory;

class PdfFactory extends Factory
{
    public function definition(): array
    {
        return [
            'lesson_id' => Lesson::factory(),
            'title' => fake()->words(3, true) . '.pdf',
            'file_path' => 'pdfs/' . fake()->uuid() . '.pdf',
        ];
    }
}