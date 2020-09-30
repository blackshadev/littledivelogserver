<?php

namespace Littledev\Tauth\Contracts;

use Illuminate\Contracts\Auth\Authenticatable;

interface TauthAuthenticatable extends Authenticatable
{
    public function getUserIdentifier(): int;
}
