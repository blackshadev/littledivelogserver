<?php

namespace Database\Seeders;

use App\Models\Buddy;
use App\Models\User;
use Illuminate\Database\Seeder;

class BuddySeeder extends Seeder
{
    public function run()
    {
        User::all()->each(function (User $user) {
            Buddy::factory()
                ->count(random_int(0, 20))
                ->state([
                    'user_id' => $user->id
                ])
                ->create();
        });
    }
}
