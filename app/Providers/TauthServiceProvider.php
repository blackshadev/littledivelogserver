<?php

namespace App\Providers;

use App\Models\User;
use App\Services\Repositories\TauthRepository;
use Illuminate\Container\Container;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Littledev\Tauth\Services\JWTService;
use Littledev\Tauth\Services\JWTServiceInterface;
use Littledev\Tauth\Services\TauthRepositoryInterface;
use Littledev\Tauth\Services\TauthService;
use Littledev\Tauth\Services\TauthServiceInterface;
use Littledev\Tauth\Support\JWTConfiguration;

class TauthServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(JWTConfiguration::class, function () {
            return (new JWTConfiguration())
                ->setKey(config('tauth.secret'))
                ->setIssuer(config('tauth.issuer'))
                ->setAudience(config('tauth.audience'))
                ->setLifetime(config('tauth.lifetime'))
                ->setSigner(config('tauth.signer'));
        });
        $this->app->bind(TauthRepositoryInterface::class, TauthRepository::class, true);
        $this->app->bind(JWTServiceInterface::class, JWTService::class);
        $this->app->singleton(TauthServiceInterface::class, function (Application $app) {
            return new TauthService(
                $app->make(JWTServiceInterface::class),
                $app->make(TauthRepositoryInterface::class)
            );
        });
        $this->app->bind(User::class, function (Container $app) {
            /** @var TauthServiceInterface $service */
            $service = $app->get(TauthServiceInterface::class);

            return $service->getUser();
        });
    }
}
