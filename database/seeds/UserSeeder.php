<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

final class UserSeeder extends Seeder
{
    public function run(): void
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
