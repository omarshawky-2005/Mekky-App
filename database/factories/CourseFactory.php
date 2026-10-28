<?php

namespace Database\Factories;

use App\Domain\Auth\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class CourseFactory extends Factory
{
    public function definition(): array
    {
        return [
            'teacher_id' => User::factory()->teacher(),
            'title' => fake()->sentence(3),
            'description' => fake()->paragraph(),
            'is_published' => fake()->boolean(80), // 80% chance to be published
        ];
    }
}