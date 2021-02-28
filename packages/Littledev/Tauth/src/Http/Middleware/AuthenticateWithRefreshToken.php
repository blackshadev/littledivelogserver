<?php

declare(strict_types=1);


namespace Littledev\Tauth\Http\Middleware;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Request;
use Littledev\Tauth\Services\TauthRepositoryInterface;
use Littledev\Tauth\Services\TauthServiceInterface;

final class AuthenticateWithRefreshToken
{
    /**
     * @var TauthRepositoryInterface
     */
    private TauthRepositoryInterface $tauthRepository;

    public function __construct(
        TauthRepositoryInterface $tauthRepository,
        TauthServiceInterface $authenticationService
    ) {
        $this->authenticationService = $authenticationService;
        $this->tauthRepository = $tauthRepository;
    }

    public function handle(Request $request, \Closure $next, ?string $optional = null)
    {
        $this->authenticate($request, $optional === 'optional');

        return $next($request);
    }

    private function authenticate(Request $request, bool $optional = false): void
    {
        $token = $request->bearerToken();
        $isValidToken = $this->tauthRepository->isRefreshTokenId($token);

        if ($optional && !$isValidToken) {
            return;
        }

        if (!$isValidToken) {
            throw new AuthorizationException('Refresh token invalid');
        }

        if (!$this->authenticationService->validateRefreshToken($token)) {
            throw new AuthorizationException('Invalid refresh token');
        }
    }
}
