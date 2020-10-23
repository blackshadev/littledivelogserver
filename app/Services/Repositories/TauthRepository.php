<?php

namespace App\Services\Repositories;

use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Littledev\Tauth\Contracts\RefreshTokenInterface;
use Littledev\Tauth\Contracts\TauthAuthenticatable;
use Littledev\Tauth\Services\JWTServiceInterface;
use Littledev\Tauth\Services\TauthRepositoryInterface;
use Ramsey\Uuid\Uuid;

class TauthRepository implements TauthRepositoryInterface
{
    private JWTServiceInterface $factory;

    public function __construct(
        JWTServiceInterface $factory
    ) {
        $this->factory = $factory;
    }

    public function createRefreshToken(TauthAuthenticatable $user): RefreshTokenInterface
    {
        if (! $user instanceof User) {
            throw new \InvalidArgumentException('Expected user for refresh token');
        }

        return RefreshToken::create(['user' => $user]);
    }

    public function findValidRefreshToken(string $uuid): ?RefreshTokenInterface
    {
        $token = RefreshToken::valid()->where('id', $uuid)->first();
        if ($token != null && ! $token instanceof RefreshTokenInterface) {
            throw new \RuntimeException('Unexpected instance');
        }

        return $token;
    }

    public function findUserByKey($key): ?TauthAuthenticatable
    {
        return User::find($key);
    }

    public function findUserByCredentials(array $credentials): ?TauthAuthenticatable
    {
        Auth::once($credentials);
        $user = Auth::user();

        if ($user && ! $user instanceof TauthAuthenticatable) {
            throw new \UnexpectedValueException('Found user of unexpected interface, ' . get_class($user));
        }

        return $user;
    }

    /**
     * @param RefreshTokenInterface|Model $refreshToken
     */
    public function expireRefreshToken(RefreshTokenInterface $refreshToken): void
    {
        $refreshToken->expire();
        $refreshToken->save();
    }

    public function isRefreshTokenId(?string $refreshTokenId): bool
    {
        if ($refreshTokenId === null) {
            return false;
        }

        return Uuid::isValid($refreshTokenId);
    }

    public function isAccessToken(?string $accessToken): bool
    {
        if ($accessToken === null) {
            return false;
        }

        return $this->factory->isJWT($accessToken);
    }
}
