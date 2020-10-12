<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class BuddyFactory extends Factory
{
    public function definition()
    {
        return [
            'text' => $this->faker->word,
            'color' => $this->faker->hexColor,
            'user_id' => User::factory(),
        ];
    }
}
