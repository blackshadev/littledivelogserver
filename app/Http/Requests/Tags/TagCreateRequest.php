<?php

declare(strict_types=1);

namespace App\Http\Requests\Tags;

use App\Http\Requests\AuthenticatedRequest;
use App\Rules\HexColor;

final class TagCreateRequest extends AuthenticatedRequest
{
    public function rules()
    {
        return [
            'text' => 'required|string',
            'color' => ['required', new HexColor()],
        ];
    }
}
