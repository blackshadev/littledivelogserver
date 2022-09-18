<?php

declare(strict_types=1);

namespace Tests\Integration\Http\Controllers\Api;

use App\Domain\Users\Repositories\UserRepository;
use App\Domain\Users\Services\UserEmailVerifier;
use App\Http\Controllers\Api\UserEmailVerifyController;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\SendVerificationEmailRequest;
use App\Models\User;
use PHPUnit\Framework\MockObject\MockObject;
use Tests\TestCase;

final class UserEmailVerifyControllerTest extends TestCase
{
    private UserEmailVerifyController $subject;

    private UserRepository & MockObject $userRepository;

    private UserEmailVerifier & MockObject $emailVerifier;

    protected function setUp(): void
    {
        parent::setUp();

        $this->userRepository = $this->createMock(UserRepository::class);
        $this->emailVerifier = $this->createMock(UserEmailVerifier::class);
        $this->subject = new UserEmailVerifyController($this->emailVerifier);
    }

    public function testItVerifies(): void
    {
        /** @var User $model */
        $model = User::factory()->notVerified()->createOne();
        $user = $model->toValueObject();

        $this->userRepository
            ->expects($this->once())
            ->method('findById')
            ->with(null)
            ->willReturn($user);

        $this->emailVerifier
            ->expects($this->once())
            ->method('verify')
            ->with($this->equalTo($user));

        $this->subject
            ->verifyEmail(new EmailVerificationRequest($this->userRepository))
            ->isRedirect($user->getOrigin()->withMessage('account.verified')->toString());
    }

    public function testResendSends(): void
    {
        /** @var User $model */
        $model = User::factory()->notVerified()->createOne();
        
        $this->userRepository
            ->expects($this->once())
            ->method('findByEmail')
            ->with($model->email)
            ->willReturn($model->toValueObject());

        $this->emailVerifier
            ->expects($this->once())
            ->method('resend')
            ->with($this->equalTo($model->toValueObject()));

        $this->subject->sendVerificationEmail(
            new SendVerificationEmailRequest($this->userRepository, [], [ 'email' => $model->email ])
        );
    }
}
