<?php

declare(strict_types=1);

namespace App\Http\Requests\Buddies;

use App\Http\Requests\AuthenticatedRequest;
use App\Rules\HexColor;

final class CreateBuddyRequest extends AuthenticatedRequest
{
    public function rules()
    {
        return [
            'text' => 'required_without:name|string',
            'name' => 'required_without:text|string',
            'color' => ['required', new HexColor()],
            'email' => 'email',
        ];
    }
}
