<?php

namespace Database\Factories;

use App\Domain\Content\Models\Course;
use Illuminate\Database\Eloquent\Factories\Factory;

class ChapterFactory extends Factory
{
    public function definition(): array
    {
        return [
            'course_id' => Course::factory(),
            'title' => fake()->words(3, true),
            'position' => fake()->numberBetween(1, 10),
        ];
    }
}