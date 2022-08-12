<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domain\Users\Commands\RegisterUser;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class RegistrationRequest extends FormRequest
{
    public function rules()
    {
        return [
            'email' => 'required|email|unique:users',
            'name' => 'required|min:3|max:255',
            'password' => 'required|min:5',
            'origin' => ['string', Rule::in(config('auth.origin.allowed'))]
        ];
    }

    public function toRegisterUser(): RegisterUser
    {
        return new RegisterUser(
            name: $this->validated('name'),
            email: $this->validated('email'),
            password: $this->validated('password'),
            origin: $this->validated('origin', config('auth.origin.default')),
        );
    }
}
