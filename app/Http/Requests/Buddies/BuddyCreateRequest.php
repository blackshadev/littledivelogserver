<?php

declare(strict_types=1);

namespace App\Http\Requests\Buddies;

use App\Rules\HexColor;
use Illuminate\Foundation\Http\FormRequest;

class BuddyCreateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'text' => 'required_without:name|string',
            'name' => 'required_without:text|string',
            'color' => ['required', new HexColor()],
        ];
    }
}
