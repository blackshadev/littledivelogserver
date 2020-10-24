<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\User;
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
        $this->middleware('auth.tuath.access')->only(['listSessions']);
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

    public function listSessions()
    {
        $user = $this->authenticationService->getUser();
        if (!$user instanceof User) {
            throw new \UnexpectedValueException('Expected user model got ' . get_class($user));
        }

        return $user->sessions()->get();
    }
}
