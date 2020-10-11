<?php

namespace Database\Factories;

use App\Models\Dive;
use Illuminate\Database\Eloquent\Factories\Factory;

class DiveTankFactory extends Factory
{
    public function definition()
    {
        return [
            'dive_id' => Dive::factory(),
            'volume' => $this->faker->randomElement([10, 12]),
            'oxygen' => $this->faker->randomElement([21, 32, 36, 40]),
            'pressure_begin' => $this->faker->numberBetween(150, 200),
            'pressure_end' => $this->faker->numberBetween(20, 150),
            'pressure_type' => 'bar',
        ];
    }
}
