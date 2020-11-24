<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Equipment;
use App\Models\EquipmentTank;
use App\Models\User;
use Illuminate\Database\Seeder;

class EquipmentSeeder extends Seeder
{
    public function run()
    {
        User::all()->each(function (User $user) {
            $equipment = Equipment::factory()
                ->state(['user_id' => $user->id])
                ->createOne();

            EquipmentTank::factory()
                ->state(['equipment_id' => $equipment->id])
                ->createOne();
        });
    }
}
