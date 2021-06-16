<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Domain\Users\Entities\User;
use App\Domain\Users\Repositories\CurrentUserRepository;
use Illuminate\Foundation\Http\FormRequest;

class AuthenticatedRequest extends FormRequest
{
    public function __construct(
        private CurrentUserRepository $currentUserRepository,
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

    public function getCurrentUser(): User
    {
        return once(fn () => $this->currentUserRepository->getCurrentUser());
    }

    public function authorize()
    {
        return $this->currentUserRepository->isLoggedIn();
    }
}
