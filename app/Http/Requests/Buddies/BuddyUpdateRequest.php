<?php

declare(strict_types=1);

namespace App\Http\Requests\Buddies;

use App\Rules\HexColor;

class BuddyUpdateRequest extends BuddyRequest
{
    public function rules()
    {
        return [
            'text' => 'required|string',
            'color' => ['required', new HexColor()],
        ];
    }
}
