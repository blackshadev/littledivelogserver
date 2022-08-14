<?php

declare(strict_types=1);

namespace App\Domain\Users\Services;

use App\Domain\Users\Entities\User;

interface UserEmailVerifier
{
    public function verify(User $user): void;

    public function resend(User $user): void;
}
