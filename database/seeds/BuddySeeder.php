<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Buddy;
use App\Models\User;
use Illuminate\Database\Seeder;

final class BuddySeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function (User $user): void {
            Buddy::factory()
                ->count(random_int(0, 20))
                ->state([
                    'user_id' => $user->id,
                ])
                ->create();
        });
    }
}
