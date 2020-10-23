<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class TagSeeder extends Seeder
{
    public function run()
    {
        User::all()->each(function (User $user) {
            Tag::factory()
                ->count(random_int(0, 20))
                ->state([
                    'user_id' => $user->id,
                ])
                ->create();
        });
    }
}
