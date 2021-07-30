<?php

declare(strict_types=1);


namespace Littledev\Tauth\Services;

use Lcobucci\JWT\Token;
use Lcobucci\JWT\UnencryptedToken;
use Littledev\Tauth\Contracts\RefreshTokenInterface;

interface JWTServiceInterface
{
    public function createTokenFor(RefreshTokenInterface $refreshToken): Token;

    public function parse(string $jwt): UnencryptedToken;

    public function isValid(Token $token): bool;

    public function getRefreshToken(UnencryptedToken $token): string;

    public function getSubjectKey(UnencryptedToken $token);

    public function isJWT(string $jwt): bool;
}
