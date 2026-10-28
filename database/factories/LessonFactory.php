<?php

namespace Database\Factories;

use App\Domain\Content\Models\Chapter;
use Illuminate\Database\Eloquent\Factories\Factory;

class LessonFactory extends Factory
{
    public function definition(): array
    {
        return [
            'chapter_id' => Chapter::factory(),
            'title' => fake()->words(4, true),
            'position' => fake()->numberBetween(1, 10),
        ];
    }
}