<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Seeder;

final class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call(CountrySeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ComputerSeeder::class);
        $this->call(PlaceSeeder::class);
        $this->call(BuddySeeder::class);
        $this->call(TagSeeder::class);
        $this->call(DiveSeeder::class);
        $this->call(EquipmentSeeder::class);
    }
}
