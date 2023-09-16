<?php

declare(strict_types=1);

namespace App\Http\Requests\Dives;

use App\Http\Requests\AuthenticatedRequest;
use Illuminate\Validation\Rule;

final class DivePatchComputerDataRequest extends AuthenticatedRequest
{
    public function rules()
    {
        return [
            'divetime' => 'required|integer|min:0',
            'max_depth' => 'required|numeric|min:0',
            'date' => 'required|date',
            'tanks' => 'array',
            'tanks.*.pressure.begin' => 'numeric|between:0,350',
            'tanks.*.pressure.end' => 'numeric|between:0,350',
            'tanks.*.pressure.type' => Rule::in(['bar', 'psi']),
            'tanks.*.volume' => 'integer',
            'tanks.*.oxygen' => 'integer|between:21,100',
            'fingerprint' => 'required|string',
            'computer_id' => [
                'required',
                'integer',
                Rule::exists('computers', 'id')
                    ->where('user_id', $this->getCurrentUser()->getId()),
            ],
            'samples' => 'required|array',
        ];
    }
}
