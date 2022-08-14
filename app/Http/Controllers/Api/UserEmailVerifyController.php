<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Domain\Users\Services\UserEmailVerifier;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\SendVerificationEmailRequest;
use Webmozart\Assert\Assert;

final class UserEmailVerifyController
{
    public function __construct(private UserEmailVerifier $emailVerifier)
    {
    }

    public function verifyEmail(EmailVerificationRequest $emailVerificationRequest)
    {
        $user = $emailVerificationRequest->findUser();
        Assert::notNull($user);

        $this->emailVerifier->verify($user);

        return redirect()->away($user->getOrigin());
    }

    public function sendVerificationEmail(SendVerificationEmailRequest $emailVerificationRequest)
    {
        $user = $emailVerificationRequest->findUser();

        $this->emailVerifier->resend($user);

        return redirect()->away($user->getOrigin());
    }
}
