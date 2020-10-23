<?php

namespace Database\Factories;

use App\Models\Dive;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class UserFactory extends Factory
{
    protected $model = User::class;

    public function definition()
    {
        return [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'email_verified_at' => now(),
            'password' => 'password', // password
            'remember_token' => Str::random(10),
        ];
    }

    public function filled()
    {
        $this->has(
            Dive::factory()->filled()->count($this->faker->numberBetween(1, 50))
        );

        return $this;
    }
}
