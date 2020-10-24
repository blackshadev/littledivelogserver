<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::factory()
            ->count(5)
            ->create();

        User::factory()
            ->state([
                'email' => 'test@test.nl',
            ])
            ->create();
    }
}
