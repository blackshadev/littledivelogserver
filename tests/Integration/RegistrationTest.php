<?php

namespace Tests\Integration;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    const REGISTRATION_URL = '/api/auth/register';
    use DatabaseTransactions;
    use WithFaker;

    public function testFailsWithNoData()
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

    public function testFailsWithInvalidEmail()
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

    public function testFailsOnDuplicatedUser()
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

    public function testSuccessfullRegistration()
    {
        $data = [
            'email' => $this->faker->email,
            'name' => $this->faker->name,
        ];

        $this->json('post', self::REGISTRATION_URL,
            array_merge($data, ['password' => $this->faker->password])
        )
            ->assertStatus(201);

        $this->assertDatabaseHas(
            'users',
            $data
        );
    }
}
