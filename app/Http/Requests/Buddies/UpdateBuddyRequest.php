<?php

declare(strict_types=1);

namespace App\Http\Requests\Buddies;

use App\Rules\HexColor;

final class UpdateBuddyRequest extends BuddyRequest
{
    public function rules()
    {
        return [
            'text' => 'required|string',
            'color' => ['required', new HexColor()],
            'email' => 'nullable|email'
        ];
    }
}
