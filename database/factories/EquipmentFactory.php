<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Equipment;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class EquipmentFactory extends Factory
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
        return $this->hasTanks(1);
    }
}
