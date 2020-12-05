<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EquipmentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'tanks' => 'array',
            'tanks.*.pressure.begin' => 'integer|between:0,350',
            'tanks.*.pressure.end' => 'integer|between:0,350',
            'tanks.*.pressure.type' => Rule::in(['bar', 'psi']),
            'tanks.*.volume' => 'integer',
            'tanks.*.oxygen' => 'integer|between:21,100',
        ];
    }
}
