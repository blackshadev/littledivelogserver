<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiveFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'max_depth'=> $this->faker->randomFloat(3, 1, 30),
            'date' => $this->faker->dateTimeThisDecade,
            'divetime' => $this->faker->numberBetween(0, 3900),
            'place_id' => null,
            'country_code' => null
        ];
    }
}
