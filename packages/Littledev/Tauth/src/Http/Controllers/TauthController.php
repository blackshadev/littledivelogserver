<?php

declare(strict_types=1);

namespace Littledev\Tauth\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Littledev\Tauth\Errors\InvalidCredentialsException;
use Littledev\Tauth\Services\TauthRepositoryInterface;
use Littledev\Tauth\Services\TauthServiceInterface;

class TauthController extends Controller
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    protected TauthServiceInterface  $authenticationService;

    protected TauthRepositoryInterface $tauthRepository;

    public function __construct(
        TauthServiceInterface $authenticationService,
        TauthRepositoryInterface  $tauthRepository
    ) {
        $this->authenticationService = $authenticationService;
        $this->middleware('auth.tuath.access:optional')->only('logout');
        $this->middleware('auth.tuath.refresh:optional')->only('logout');
        $this->middleware('auth.tuath.refresh')->only('access');

        $this->tauthRepository = $tauthRepository;
    }

    public function access(): array
    {
        $refreshToken = $this->authenticationService->getRefreshToken();

        if ($refreshToken === null) {
            throw new \UnexpectedValueException("Unexpected null value for refresh token");
        }

        $accessToken = $this->authenticationService->createAccessToken($refreshToken);

        return [
            "access_token" => (string)$accessToken
        ];
    }

    public function refresh(Request  $request): array
    {
        $credentials = $request->only('email', 'password');

        $user = $this->tauthRepository->findUserByCredentials($credentials);
        if ($user === null) {
            throw new InvalidCredentialsException();
        }

        $refreshToken = $this->authenticationService->createRefreshToken($user);
        $accessToken = $this->authenticationService->createAccessToken($refreshToken);

        return [
            "refresh_token" => (string)$refreshToken,
            "access_token" => $accessToken->toString(),
        ];
    }

    public function logout(): void
    {
        $refreshToken = $this->authenticationService->getRefreshToken();

        if ($refreshToken) {
            $this->tauthRepository->expireRefreshToken($refreshToken);
        }
    }
}
