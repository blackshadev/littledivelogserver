<?php

declare(strict_types=1);

namespace App\Services\Users;

use App\Domain\Users\Entities\User;
use App\Domain\Users\Services\UserEmailVerifier;
use App\Error\Auth\AlreadyVerified;
use App\Models\User as UserModel;
use Illuminate\Auth\Events\Verified;

final class LaravelUserEmailVerifier implements UserEmailVerifier
{
    public function verify(User $user): void
    {
        $model = UserModel::find($user->getId());

        if ($model->hasVerifiedEmail()) {
            throw new AlreadyVerified();
        }

        $model->markEmailAsVerified();

        event(new Verified($model));
    }

    public function resend(User $user): void
    {
        $model = UserModel::find($user->getId());

        if ($model->hasVerifiedEmail()) {
            throw new AlreadyVerified();
        }

        $model->sendEmailVerificationNotification();
    }
}
