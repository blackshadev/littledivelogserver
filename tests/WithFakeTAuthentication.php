<?php


namespace Tests;


use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Littledev\Tauth\Services\TauthServiceInterface;
use Mockery\Mock;

trait WithFakeTAuthentication
{
    /** @var TauthServiceInterface|Mock  */
    private TauthServiceInterface $tauthService;

    private function fakedTauth() {
        $this->tauthService = \Mockery::mock(TauthServiceInterface::class);
        $this->app->instance(TauthServiceInterface::class, $this->tauthService);

        $this->tauthService->shouldReceive('validateAccessToken')->andReturnFalse()->byDefault();
        $this->tauthService->shouldReceive('getUser')->andReturn(null)->byDefault();
    }

    private function fakeAccessTokenFor(User $user)
    {
        $this->tauthService->shouldReceive('validateAccessToken')->andReturnTrue();
        $this->tauthService->shouldReceive('getUser')->andReturn($user);
        Auth::setUser($user);
    }

}