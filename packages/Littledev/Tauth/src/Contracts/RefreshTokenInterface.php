<?php

declare(strict_types=1);

namespace Littledev\Tauth\Contracts;

interface RefreshTokenInterface extends JWTSubject
{
    public function toString(): string;

    public function getToken(): string;

    public function isExpired(): bool;

    public function expire(): void;
}
