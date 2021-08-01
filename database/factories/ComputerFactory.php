<?php

declare(strict_types=1);

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

final class ComputerFactory extends Factory
{
    public function definition()
    {
        return [
            'serial' => $this->faker->unique()->randomNumber(),
            'vendor' => $this->faker->lastName,
            'model' => $this->faker->randomNumber(),
            'type' => $this->faker->randomNumber(),
            'name' => $this->faker->firstName,
            'last_read' => $this->faker->dateTimeThisYear,
            'last_fingerprint' => $this->faker->word,
        ];
    }
}
