<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Dive;
use App\Models\DiveTank;
use App\Models\Place;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

final class DiveFactory extends Factory
{
    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'max_depth' => $this->faker->randomFloat(3, 1, 30),
            'date' => $this->faker->dateTimeThisDecade,
            'divetime' => $this->faker->numberBetween(0, 3900),
            'place_id' => null,
            'country_code' => null,
        ];
    }

    public function filled()
    {
        return $this
            ->state(function () {
                $place = Place::inRandomOrder()->firstOrFail();

                return [
                    'place_id' => $place->id,
                    'country_code' => $place->country_code,
                ];
            })
            ->has(DiveTank::factory(), 'tanks')
            ->afterCreating(function (Dive $dive): void {
                /** @var User $user */
                $user = $dive->user;

                $dive->buddies()->attach(
                    $user->buddies()
                        ->inRandomOrder()
                        ->take(random_int(1, 5))
                        ->get('id')
                );

                $dive->tags()->attach(
                    $user->tags()
                        ->inRandomOrder()
                        ->take(random_int(1, 5))
                        ->get('id')
                );
            });
    }

    public function withComputer(): self
    {
        return $this->afterMaking(function (Dive $dive): void {
            /** @var User $user */
            $user = $dive->user;
            $dive->computer()->associate($user->computers()->inRandomOrder()->first());
        });
    }
}
