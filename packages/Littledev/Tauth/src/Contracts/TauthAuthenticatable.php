<?php

declare(strict_types=1);

namespace Littledev\Tauth\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface TauthAuthenticatable extends Authenticatable
{
    public function getUserIdentifier(): int;
}
