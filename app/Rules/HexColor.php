<?php

declare(strict_types=1);

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

final class HexColor implements Rule
{
    public function passes($attribute, $value)
    {
        return preg_match('/\#[0-9abcdef]{6}/i', $value);
    }

    public function message()
    {
        return trans('validation.hex_color');
    }
}
