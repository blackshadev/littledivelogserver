<?php

declare(strict_types=1);

namespace App\Http\Requests\Places;

use App\Http\Requests\AuthenticatedRequest;

class SearchPlaceRequest extends AuthenticatedRequest
{
    public function rules()
    {
        return [
            'country' => 'string',
            'keywords' => 'string',
        ];
    }
}
