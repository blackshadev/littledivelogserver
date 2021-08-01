<?php

declare(strict_types=1);

namespace Littledev\Tauth\Http\Middleware;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Auth\Middleware\AuthenticatesRequests;
use Illuminate\Http\Request;
use Littledev\Tauth\Services\TauthServiceInterface;

final class AuthenticateWithAccessToken implements AuthenticatesRequests
{
    private TauthServiceInterface $authenticationService;

    public function __construct(
        TauthServiceInterface $authenticationService
    ) {
        $this->authenticationService = $authenticationService;
    }

    public function handle(Request $request, \Closure $next, ?string $optional = null)
    {
        $this->authenticate($request, $optional === 'optional');

        return $next($request);
    }

    private function authenticate(Request $request, bool $optional = false): void
    {
        $token = $request->bearerToken();

        $valid = false;
        try {
            $valid = $this->authenticationService->validateAccessToken($token);
        } catch (\Throwable $exception) {
            if (!$optional) {
                throw $exception;
            }
        }

        if (!$optional && !$valid) {
            throw new AuthorizationException("Invalid JWT");
        }
    }
}
