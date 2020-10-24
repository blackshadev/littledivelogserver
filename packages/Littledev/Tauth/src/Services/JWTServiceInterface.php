<?php

declare(strict_types=1);


namespace Littledev\Tauth\Services;

use Lcobucci\JWT\Token;
use Littledev\Tauth\Contracts\RefreshTokenInterface;

interface JWTServiceInterface
{
    public function createTokenFor(RefreshTokenInterface $refreshToken): Token;

    public function parse(string $jwt): Token;

    public function isValid(Token $token): bool;

    public function getRefreshToken(Token $token): string;

    public function getSubjectKey(Token $token);

    public function isJWT(string $jwt): bool;
}
