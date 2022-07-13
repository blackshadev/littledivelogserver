<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Computer;
use App\Models\User;
use Illuminate\Database\Seeder;

final class ComputerSeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function (User $user): void {
            Computer::factory()
                ->count(random_int(0, 10))
                ->state(['user_id' => $user->id])
                ->create();
        });
    }
}
