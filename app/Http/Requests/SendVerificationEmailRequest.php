<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domain\Users\Entities\User;
use App\Domain\Users\Repositories\UserRepository;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

final class SendVerificationEmailRequest extends FormRequest
{
    public function __construct(
        private UserRepository $userRepository,
        array $query = [],
        array $request = [],
        array $attributes = [],
        array $cookies = [],
        array $files = [],
        array $server = [],
        $content = null
    ) {
        parent::__construct($query, $request, $attributes, $cookies, $files, $server, $content);
    }

    public function rules(): array
    {
        return [
            "email" => ['required', 'email', Rule::exists('users', 'email')]
        ];
    }

    public function findUser(): User
    {
        return $this->userRepository->findByEmail($this->validated('email'));
    }
}
