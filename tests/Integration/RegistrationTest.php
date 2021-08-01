<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

final class RegistrationTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public const REGISTRATION_URL = '/api/auth/register';

    public function testFailsWithNoData(): void
    {
        $this->json('post', self::REGISTRATION_URL)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                    'name' => ['The name field is required.'],
                ],
            ]);
    }

    public function testFailsWithInvalidEmail(): void
    {
        $this->json('post', self::REGISTRATION_URL, [
            'email' => $this->faker->word,
            'name' => $this->faker->name,
            'password' => $this->faker->password,
        ])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => ['The email must be a valid email address.'],
                ],
            ]);
    }

    public function testFailsOnDuplicatedUser(): void
    {
        /** @var User $user */
        $user = User::factory()->create();
        $this->json('post', self::REGISTRATION_URL, [
            'email' => $user->email,
            'name' => $this->faker->name,
            'password' => $this->faker->password,
        ])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => ['The email has already been taken.'],
                ],
            ]);
    }

    public function testSuccessfullRegistration(): void
    {
        $data = [
            'email' => $this->faker->email,
            'name' => $this->faker->name,
        ];

        $this->json(
            'post',
            self::REGISTRATION_URL,
            array_merge($data, ['password' => $this->faker->password])
        )
            ->assertStatus(201);

        $this->assertDatabaseHas(
            'users',
            $data
        );
    }
}
