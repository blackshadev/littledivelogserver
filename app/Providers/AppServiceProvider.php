<?php

declare(strict_types=1);

namespace App\Providers;

use App\Domain\Users\Entities\User;
use App\Domain\Users\Services\UserEmailVerifier;
use App\Domain\Users\Services\UserRegistrator;
use App\Mail\UserEmailVerification;
use App\Services\Users\LaravelUserEmailVerifier;
use App\Services\Users\LaravelUserRegistrator;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\ServiceProvider;

final class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(UserRegistrator::class, LaravelUserRegistrator::class);
        $this->app->bind(UserEmailVerifier::class, LaravelUserEmailVerifier::class);

        VerifyEmail::toMailUsing(static function ($notifiable, $url) {
            return new UserEmailVerification(User::fromArray($notifiable->toArray()), $url);
        });
    }

    public function boot(): void
    {
    }
}
