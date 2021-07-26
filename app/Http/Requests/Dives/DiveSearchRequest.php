<?php

declare(strict_types=1);

namespace App\Http\Requests\Dives;

use App\Http\Requests\AuthenticatedRequest;

class DiveSearchRequest extends AuthenticatedRequest
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
