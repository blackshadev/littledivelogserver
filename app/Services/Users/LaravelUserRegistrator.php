<?php

declare(strict_types=1);

namespace App\Services\Users;

use App\Domain\Users\Commands\RegisterUser;
use App\Domain\Users\Entities\User;
use App\Domain\Users\Services\UserRegistrator;
use App\Domain\Users\ValueObjects\OriginUrl;
use App\Models\User as UserModel;
use Illuminate\Auth\Events\Registered;

final class LaravelUserRegistrator implements UserRegistrator
{
    public function register(RegisterUser $registerUser): User
    {
        $origin = OriginUrl::fromString($registerUser->origin);
        $user = UserModel::create([
            'name' => $registerUser->name,
            'email' => $registerUser->email,
            'password' => $registerUser->password,
            'origin' => $origin->toString(),
        ]);

        event(new Registered($user));

        return new User($user->id, $user->name, $user->email, $origin);
    }
}
