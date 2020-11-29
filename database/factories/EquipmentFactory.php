<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\EquipmentTank;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class EquipmentFactory extends Factory
{
    protected $model = Equipment::class;

    public function definition()
    {
        return [
            'user_id' => User::factory()
        ];
    }

    public function filled(): self
    {
        return $this->has(EquipmentTank::factory()->count(1));
    }
}
