<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Rules\HexColor;
use Illuminate\Foundation\Http\FormRequest;

class TagCreateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'text' => 'required|string',
            'color' => ['required', new HexColor()],
        ];
    }
}
