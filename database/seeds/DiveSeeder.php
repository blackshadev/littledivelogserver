<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\Dive;
use App\Models\DiveTank;
use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Seeder;

final class DiveSeeder extends Seeder
{
    public function run(): void
    {
        User::all()->each(function (User $user): void {
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
                ->sequence(fn () => [ 'fingerprint' => random_int(0, 1) === 1 ? 'deadbeef' : null ])
                ->afterMaking(function (Dive $dive) use ($user): void {
                    if (random_int(0, 3) < 3) {
                        $dive->computer()->associate($user->computers()->inRandomOrder()->first());
                    } else {
                        $dive->fingerprint = null;
                    }
                })
                ->create()
                ->each(function (Dive $dive) use ($user): void {
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
