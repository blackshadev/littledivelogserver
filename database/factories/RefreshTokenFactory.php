<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Eloquent\Factories\Factory;

final class RefreshTokenFactory extends Factory
{
    public function expired()
    {
        return $this->state(function (array $attributes) {
            return [
                'expired_at' => (CarbonImmutable::now()->subMinutes($this->faker->numberBetween(1, 210))),
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
