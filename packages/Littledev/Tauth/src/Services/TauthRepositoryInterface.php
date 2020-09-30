<?php

namespace Littledev\Tauth\Services;

use Littledev\Tauth\Contracts\RefreshTokenInterface;
use Littledev\Tauth\Contracts\TauthAuthenticatable;

interface TauthRepositoryInterface
{
    public function expireRefreshToken(RefreshTokenInterface  $refreshToken): void;
    public function isAccessToken(?string $accessToken): bool;
    public function isRefreshTokenId(?string $refreshTokenId): bool;
    public function createRefreshToken(TauthAuthenticatable $user): RefreshTokenInterface;
    public function findValidRefreshToken(string $uuid): ?RefreshTokenInterface;
    public function findUserByKey($key): ?TauthAuthenticatable;
    public function findUserByCredentials(array $credentials): ?TauthAuthenticatable;
}
