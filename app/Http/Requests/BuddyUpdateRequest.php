<?php

namespace App\Http\Requests;

use App\Rules\HexColor;
use Illuminate\Foundation\Http\FormRequest;

class BuddyUpdateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'text' => 'required|string',
            'color' => ['required', new HexColor()],
        ];
    }
}
