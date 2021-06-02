<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class DiveSearchRequest extends FormRequest
{
    public function rules()
    {
        return [
            'keywords' => 'string',
            'date_after' => 'date',
            'date_before' => 'date',
            'tags' => 'array',
            'tags.*' => 'integer',
            'buddies' => 'array',
            'buddies.*' => 'integer',
            'place' => 'integer'
        ];
    }
}
