<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ComputerFactory extends Factory
{
    public function definition()
    {
        return [
            'serial' => $this->faker->randomNumber(),
            'vendor' => $this->faker->lastName,
            'model' => $this->faker->randomNumber(),
            'type' => $this->faker->randomNumber(),
            'name' => $this->faker->firstName,
            'last_read' => $this->faker->dateTimeThisYear,
            'last_fingerprint' => $this->faker->word,
        ];
    }
}
