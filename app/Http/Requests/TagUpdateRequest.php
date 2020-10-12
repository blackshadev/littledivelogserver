<?php

namespace App\Http\Requests;

use App\Rules\HexColor;
use Illuminate\Foundation\Http\FormRequest;

class TagUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string',
            'color' => ['required', new HexColor()],
        ];
    }
}
