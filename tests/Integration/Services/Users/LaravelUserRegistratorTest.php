<?php

declare(strict_types=1);

namespace Tests\Integration\Services\Users;

use App\Domain\Users\Commands\RegisterUser;
use App\Services\Users\LaravelUserRegistrator;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

final class LaravelUserRegistratorTest extends TestCase
{
    use DatabaseTransactions;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new LaravelUserRegistrator();
    }

    public function testRegisterCreatesANewUser(): void
    {
        $registerUser = new RegisterUser(
            name: ':test:',
            email: 'user@example.com',
            password: ':password:',
            origin: 'https://origin.com',
        );

        $this->subject->register($registerUser);

        $this->assertDatabaseHas('users', [
            'name' => $registerUser->name,
            'email' => $registerUser->email,
            'origin' => $registerUser->origin,
            'email_verified_at' => null,
        ]);
    }

    public function testRegisterEmitEvent(): void
    {
        Event::fake();

        $registerUser = new RegisterUser(
            name: ':test:',
            email: 'user@example.com',
            password: ':password:',
            origin: 'https://origin.com',
        );

        $this->subject->register($registerUser);

        Event::assertDispatched(Registered::class);
    }
}
