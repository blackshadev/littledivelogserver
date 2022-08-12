<?php

declare(strict_types=1);

namespace App\Http\Controllers\Api;

use App\Application\ViewModels\ApiModels\UserSessionViewModel;
use App\Domain\Users\Services\UserRegistrator;
use App\Http\Requests\EmailVerificationRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistrationRequest;
use App\Models\RefreshToken;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Littledev\Tauth\Http\Controllers\TauthController;
use Littledev\Tauth\Services\TauthRepositoryInterface;
use Littledev\Tauth\Services\TauthServiceInterface;
use Symfony\Component\HttpFoundation\Response;
use Webmozart\Assert\Assert;

final class AuthController extends TauthController
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

    public function register(RegistrationRequest $request, UserRegistrator $registrator)
    {
        $registrator->register($request->toRegisterUser());

        return response()->noContent(Response::HTTP_CREATED);
    }

    public function deleteSession(RefreshToken $refreshToken): void
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

    public function verifyEmail(EmailVerificationRequest $emailVerificationRequest)
    {
        $user = $emailVerificationRequest->findUser();
        Assert::notNull($user);

        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            event(new Verified($user));
        }

        return redirect()->away($user->origin);
    }
}
