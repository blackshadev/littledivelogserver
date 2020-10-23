<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ComputerCreateRequest extends FormRequest
{
    public function rules()
    {
        return [
            'serial' => 'required|integer',
            'vendor' => 'required|string',
            'model' => 'required|integer',
            'type' => 'required|integer',
            'name' => 'required|string',
        ];
    }
}
