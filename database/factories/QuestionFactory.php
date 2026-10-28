<?php

namespace Database\Factories;

use App\Domain\Content\Models\Pdf;
use Illuminate\Database\Eloquent\Factories\Factory;

class QuestionFactory extends Factory
{
    public function definition(): array
    {
        return [
            'pdf_id' => Pdf::factory(),
            'question_number' => fake()->numberBetween(1, 50),
            'reports_count' => fake()->numberBetween(0, 20),
            'last_reported_at' => fake()->optional()->dateTime(),
        ];
    }
}