<?php

declare(strict_types=1);

namespace App\Http\Requests\Equipment;

use Illuminate\Validation\Rule;

final class UpdateEquipmentRequest extends EquipmentRequest
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
