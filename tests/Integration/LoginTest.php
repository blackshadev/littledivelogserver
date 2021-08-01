<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

final class LoginTest extends TestCase
{
    use WithFaker;
    use DatabaseTransactions;

    public const LOGIN_URL = '/api/auth/sessions/';

    public function testInvalidRequest(): void
    {
        $this->json('post', self::LOGIN_URL, [])
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The given data was invalid.',
                'errors' => [
                    'email' => ['The email field is required.'],
                    'password' => ['The password field is required.'],
                ],
            ]);
    }

    public function testInvalidCredentials(): void
    {
        $this->json('post', self::LOGIN_URL, [
            'email' => $this->faker->email,
            'password' => $this->faker->password,
        ])
            ->assertStatus(401)
            ->assertJson([
                'message' => 'Invalid credentials.',
            ]);
    }

    public function testValidLogin(): void
    {
        $password = $this->faker->password;
        /** @var User $user */
        $user = User::factory()->create([
            'password' => $password,
        ]);
        $response = $this->json('post', self::LOGIN_URL, [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response
            ->assertStatus(200)
            ->assertJsonStructure([
                'access_token',
                'refresh_token',
            ]);

        $refreshToken = $response->json('refresh_token');
        $this->assertDatabaseHas('refresh_tokens', [
            'id' => $refreshToken,
            'user_id' => $user->id,
            'expired_at' => null,
        ]);
    }
}
