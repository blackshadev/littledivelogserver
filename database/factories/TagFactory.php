<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class TagFactory extends Factory
{
    public function definition()
    {
        return [
            'color' => $this->faker->hexColor,
            'text' => $this->faker->word,
            'user_id' => User::factory(),
        ];
    }
}
