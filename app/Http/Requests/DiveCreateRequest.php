<?php

declare(strict_types=1);

namespace App\Http\Requests;

class DiveCreateRequest extends DiveUpdateRequest
{
    public function rules()
    {
        return parent::rules() + [
            'computer_id' => 'integer|exists:computers',
            'fingerprint' => 'required_with:computer_id|string',
        ];
    }
}
