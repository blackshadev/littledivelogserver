<?php

namespace Littledev\Tauth\Contracts;

interface RefreshTokenInterface extends JWTSubject
{
    public function getToken(): string;
    public function isExpired(): bool;
    public function expire(): void;
    public function __toString(): string;
}
