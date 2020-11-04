<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiveMergeRequest extends FormRequest
{
    public function rules()
    {
        return [
            'dives' => 'array|min:2',
            'dives.*' => 'required|exists:users'
        ];
    }
}
