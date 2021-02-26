<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Dive;
use App\Models\DiveTank;
use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Seeder;

class DiveSeeder extends Seeder
{
    public function run()
    {
        User::all()->each(function (User $user) {
            Dive::factory()
                ->count(random_int(0, 100))
                ->state(function () {
                    $place = Place::inRandomOrder()->firstOrFail();

                    return [
                        'place_id' => $place->id,
                        'country_code' => $place->country_code,
                    ];
                })
                ->state(['user_id' => $user->id])
                ->create()
                ->each(function (Dive $dive) use ($user) {
                    $dive->buddies()->attach(
                        $user->buddies()
                            ->inRandomOrder()
                            ->take(random_int(0, 5))
                            ->get('id')
                    );

                    $dive->tags()->attach(
                        $user->tags()
                            ->inRandomOrder()
                            ->take(random_int(0, 5))
                            ->get('id')
                    );

                    /** @var DiveTank $tank */
                    $tank = DiveTank::factory()->createOne([
                        'dive_id' => $dive->id,
                    ]);

                    $dive->tanks()->save($tank);
                });
        });
    }
}
