<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ChatMessageFactory extends Factory
{
    public function definition(): array
    {
        return [
            'message' => fake()->sentence(),
            'seen_by' => null,
            'chat_message_id' => null,
            'upload_id' => null,
        ];
    }
}
