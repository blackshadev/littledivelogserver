<?php

declare(strict_types=1);

namespace App\Http\Requests\Users;

use App\Http\Requests\AuthenticatedRequest;

final class UpdatePasswordRequest extends AuthenticatedRequest
{
    public function rules()
    {
        return [
            'old' => 'required',
            'new' => 'required|min:5',
        ];
    }
}
