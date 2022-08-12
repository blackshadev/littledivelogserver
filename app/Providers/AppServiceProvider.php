<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Users\Services\UserRegistrator;
use App\Repositories\Users\LaravelUserRegistrator;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRegistrator::class, LaravelUserRegistrator::class);
    }

    public function boot(): void
    {
    }
}
