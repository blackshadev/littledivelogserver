<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

final class EmailVerificationRequest extends FormRequest
{
    public function authorize(): bool
    {
        $user = $this->findUser();
        if ($user === null) {
            return false;
        }
        if (!hash_equals(
            (string) $this->route('hash'),
            sha1($user->getEmailForVerification())
        )) {
            return false;
        }

        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function findUser(): User|null
    {
        return User::find($this->route('id'));
    }
}
