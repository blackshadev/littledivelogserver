<?php

declare(strict_types=1);

namespace Littledev\Tauth\Services;

use Illuminate\Support\Facades\Auth;
use Lcobucci\JWT\Token;
use Littledev\Tauth\Contracts\RefreshTokenInterface;
use Littledev\Tauth\Contracts\TauthAuthenticatable;
use Littledev\Tauth\Errors\InvalidJWTException;
use Littledev\Tauth\Errors\NoSuchUserException;

final class TauthService implements TauthServiceInterface
{
    private JWTServiceInterface $JWTFactory;

    private TauthRepositoryInterface $tauthRepository;

    private ?Token $token = null;

    private ?RefreshTokenInterface $refreshToken = null;

    public function __construct(
        JWTServiceInterface $JWTService,
        TauthRepositoryInterface $tauthRepository
    ) {
        $this->JWTFactory = $JWTService;
        $this->tauthRepository = $tauthRepository;
    }

    public function createRefreshToken(TauthAuthenticatable $user): RefreshTokenInterface
    {
        return $this->tauthRepository->createRefreshToken($user);
    }

    public function createAccessToken(RefreshTokenInterface $sessionToken): Token
    {
        return $this->JWTFactory->createTokenFor($sessionToken);
    }

    public function validateRefreshToken(string $refreshTokenId): bool
    {
        if (!$this->tauthRepository->isRefreshTokenId($refreshTokenId)) {
            return false;
        }

        $refreshToken = $this->tauthRepository->findValidRefreshToken($refreshTokenId);
        $this->refreshToken = $refreshToken;

        return $refreshToken !== null;
    }

    public function getAccessToken(): ?Token
    {
        return $this->token;
    }

    public function getRefreshToken(): ?RefreshTokenInterface
    {
        if ($this->refreshToken) {
            return $this->refreshToken;
        }

        if ($this->token) {
            $refreshTokenId = $this->JWTFactory->getRefreshToken($this->token);
            $refreshToken = $this->tauthRepository->findValidRefreshToken($refreshTokenId);
            return $this->refreshToken = $refreshToken;
        }

        return null;
    }

    public function isAuthenticated(): bool
    {
        return $this->token !== null;
    }

    public function getUser(): ?TauthAuthenticatable
    {
        if (!$this->token) {
            return null;
        }

        $userKey = $this->JWTFactory->getSubjectKey($this->token);
        $user = $this->tauthRepository->findUserByKey($userKey);

        if ($user === null) {
            throw new NoSuchUserException('Unable to find user from given token');
        }

        return $user;
    }

    public function validateAccessToken(?string $jwtData): bool
    {
        if (!$this->tauthRepository->isAccessToken($jwtData)) {
            throw InvalidJWTException::malformed();
        }

        $token = $this->parseAccessToken($jwtData);

        if (!$this->JWTFactory->isValid($token)) {
            throw InvalidJWTException::invalid();
        }

        $this->token = $token;

        Auth::setUser($this->getUser());

        return true;
    }

    private function parseAccessToken(string $jwtData): Token
    {
        try {
            return $this->JWTFactory->parse($jwtData);
        } catch (\Throwable $err) {
            throw new InvalidJWTException($err->getMessage());
        }
    }
}
