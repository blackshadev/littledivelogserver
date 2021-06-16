<?php

declare(strict_types=1);

namespace App\Http\Requests\Users;

use App\Http\Requests\AuthenticatedRequest;

class UpdateProfileRequest extends AuthenticatedRequest
{
    public function rules()
    {
        return [
            'name' => 'required|min:3|max:255',
        ];
    }
}
