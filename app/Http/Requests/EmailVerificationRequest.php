<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domain\Users\Entities\User;
use App\Domain\Users\Repositories\UserRepository;
use App\Error\UserNotFound;
use Illuminate\Foundation\Http\FormRequest;

final class EmailVerificationRequest extends FormRequest
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

    public function authorize(): bool
    {
        $user = $this->findUser();
        if ($user === null) {
            return false;
        }
        if (!hash_equals(
            (string) $this->route('hash'),
            sha1($user->getEmail())
        )) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [
            'id' => 'integer'
        ];
    }

    public function findUser(): User|null
    {
        try {
            return $this->userRepository->findById((int)$this->route('id'));
        } catch (UserNotFound) {
            return null;
        }
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['id' => $this->route('id')]);
    }
}
