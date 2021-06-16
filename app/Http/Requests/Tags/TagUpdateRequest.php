<?php

declare(strict_types=1);

namespace App\Http\Requests\Tags;

use App\Rules\HexColor;

class TagUpdateRequest extends TagRequest
{
    public function rules()
    {
        return [
            'text' => 'required|string',
            'color' => ['required', new HexColor()],
        ];
    }
}
