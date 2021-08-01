<?php

declare(strict_types=1);

namespace Tests;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Littledev\Tauth\Services\TauthServiceInterface;
use Mockery\MockInterface;

trait WithFakeTAuthentication
{
    /** @var TauthServiceInterface|MockInterface */
    private TauthServiceInterface $tauthService;

    private function fakedTauth(): void
    {
        $this->tauthService = \Mockery::mock(TauthServiceInterface::class);
        $this->app->instance(TauthServiceInterface::class, $this->tauthService);

        $this->tauthService->shouldReceive('validateAccessToken')->andReturnFalse()->byDefault();
        $this->tauthService->shouldReceive('getUser')->andReturn(null)->byDefault();
    }

    private function fakeAccessTokenFor(User $user): void
    {
        $this->tauthService->shouldReceive('validateAccessToken')->andReturnTrue();
        $this->tauthService->shouldReceive('getUser')->andReturn($user);
        Auth::setUser($user);
    }
}
