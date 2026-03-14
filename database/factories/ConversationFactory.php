<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ConversationFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => null,
            'is_group' => false,
        ];
    }

    public function group(): static
    {
        return $this->state(fn () => [
            'name' => fake()->words(2, true),
            'is_group' => true,
        ]);
    }
}
