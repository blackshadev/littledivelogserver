<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\RefreshToken;
use App\Models\User;
use App\ViewModels\ApiModels\UserSessionViewModel;
use Littledev\Tauth\Http\Controllers\TauthController;
use Littledev\Tauth\Services\TauthRepositoryInterface;
use Littledev\Tauth\Services\TauthServiceInterface;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends TauthController
{
    public function __construct(
        TauthServiceInterface $authenticationService,
        TauthRepositoryInterface $tauthRepository
    ) {
        parent::__construct($authenticationService, $tauthRepository);
        $this->middleware('auth.tuath.access')->only(['listSessions', 'deleteSession']);
    }

    public function login(LoginRequest $request)
    {
        return $this->refresh($request);
    }

    public function register(RegistrationRequest $request)
    {
        User::create($request->all());

        return response()->noContent(Response::HTTP_CREATED);
    }

    public function deleteSession(RefreshToken $refreshToken)
    {
        $this->authorize('delete', $refreshToken);

        $refreshToken->delete();
    }

    public function listSessions()
    {
        $user = $this->authenticationService->getUser();
        if (!$user instanceof User) {
            throw new \UnexpectedValueException('Expected user model got ' . get_class($user));
        }

        return UserSessionViewModel::fromCollection($user->sessions);
    }
}
