<?php

declare(strict_types=1);


namespace Littledev\Tauth\Contracts;

interface JWTSubject
{
    public function getJWTSubject();

    public function getJWTExtraClaims(): array;
}
