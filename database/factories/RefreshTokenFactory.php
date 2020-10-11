<?php

namespace Database\Factories;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;

class RefreshTokenFactory extends Factory
{
    public function expired()
    {
        return $this->state(function (array $attributes) {
            return [
                'expired_at' => (Carbon::now()->subMinutes($this->faker->numberBetween(1, 210))),
            ];
        });
    }

    public function definition()
    {
        return [
            'id' => $this->faker->uuid,
            'user_id' => User::factory(),
            'expired_at' => null,
        ];
    }
}
