<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiveCreateRequest extends DiveUpdateRequest
{
    public function rules()
    {
        return parent::rules() + [
            'computer_id' => 'integer|exists:computers',
            'fingerprint' => 'required_with:computer_id|string'
        ];
    }
}
