<?php

declare(strict_types=1);


namespace Littledev\Tauth\Services;

use Lcobucci\JWT\Token;
use Littledev\Tauth\Contracts\RefreshTokenInterface;
use Littledev\Tauth\Contracts\TauthAuthenticatable;

interface TauthServiceInterface
{
    public function isAuthenticated(): bool;

    public function getUser(): ?TauthAuthenticatable;

    public function getAccessToken(): ?Token;

    public function getRefreshToken(): ?RefreshTokenInterface;

    public function createRefreshToken(TauthAuthenticatable $user): RefreshTokenInterface;

    public function createAccessToken(RefreshTokenInterface $sessionToken): Token;

    public function validateRefreshToken(string $refreshTokenId): bool;

    public function validateAccessToken(?string $jwtData): bool;
}
