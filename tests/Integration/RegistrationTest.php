<?php

declare(strict_types=1);

namespace Tests\Integration;

use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class RegistrationTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    public const REGISTRATION_URL = '/api/auth/register';

    protected function setUp(): void
    {
        parent::setUp();
        Mail::fake();
    }

    public function testFailsWithNoData(): void
    {
        $this->json('post', self::REGISTRATION_URL)
            ->assertStatus(422)
            ->assertJson([
                'message' => 'The email field is required. (and 2 more errors)',
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
                'message' => 'The email must be a valid email address.',
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
                'message' => 'The email has already been taken.',
                'errors' => [
                    'email' => ['The email has already been taken.'],
                ],
            ]);
    }

    public function testSuccessfulRegistrationCreatesUser(): void
    {
        Mail::fake();

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

    public function testSuccessfulRegistrationSentsNotification(): void
    {
        Notification::fake();

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

        $user = User::query()->where('email', '=', $data['email'])->get();

        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
