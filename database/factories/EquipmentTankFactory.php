<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Equipment;
use Illuminate\Database\Eloquent\Factories\Factory;

final class EquipmentTankFactory extends Factory
{
    public function definition()
    {
        return [
            'equipment_id' => Equipment::factory(),
            'volume' => $this->faker->randomElement([10, 12]),
            'oxygen' => $this->faker->randomElement([21, 32, 36, 40]),
            'pressure_begin' => $this->faker->numberBetween(150, 200),
            'pressure_end' => $this->faker->numberBetween(20, 150),
            'pressure_type' => 'bar',
        ];
    }
}
