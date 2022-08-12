<?php

declare(strict_types=1);

namespace App\Domain\Users\Services;

use App\Domain\Users\Commands\RegisterUser;
use App\Domain\Users\Entities\User;

interface UserRegistrator
{
    public function register(RegisterUser $registerUser): User;
}
