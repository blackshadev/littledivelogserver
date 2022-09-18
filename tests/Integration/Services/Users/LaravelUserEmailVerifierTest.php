<?php

declare(strict_types=1);

namespace Tests\Integration\Services\Users;

use App\Error\Auth\AlreadyVerified;
use App\Models\User as UserModel;
use App\Services\Users\LaravelUserEmailVerifier;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

final class LaravelUserEmailVerifierTest extends TestCase
{
    use DatabaseTransactions;

    private LaravelUserEmailVerifier $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new LaravelUserEmailVerifier();
    }

    public function testVerifyThrowsOnVerifiedUser(): void
    {
        /** @var UserModel $user */
        $user = UserModel::factory()->createOne();

        $this->expectException(AlreadyVerified::class);

        $this->subject->verify($user->toValueObject());
    }

    public function testVerifyUser(): void
    {
        /** @var UserModel $user */
        $user = UserModel::factory()->notVerified()->createOne();

        $this->subject->verify($user->toValueObject());

        $user->refresh();
        self::assertTrue($user->hasVerifiedEmail());
    }

    public function testResendThrowsOnVerifiedUser(): void
    {
        /** @var UserModel $user */
        $user = UserModel::factory()->createOne();

        $this->expectException(AlreadyVerified::class);

        $this->subject->resend($user->toValueObject());
    }

    public function testResendMails(): void
    {
        Notification::fake();

        /** @var UserModel $user */
        $user = UserModel::factory()->notVerified()->createOne();

        $this->subject->resend($user->toValueObject());

        Notification::assertSentTo($user, VerifyEmail::class);
    }
}
