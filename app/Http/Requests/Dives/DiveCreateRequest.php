<?php

declare(strict_types=1);

namespace App\Http\Requests\Dives;

use App\Http\Requests\AuthenticatedRequest;
use App\Rules\HexColor;
use Illuminate\Validation\Rule;

final class DiveCreateRequest extends AuthenticatedRequest
{
    public function rules()
    {
        return [
            'divetime' => 'required|integer|min:0',
            'max_depth' => 'required|numeric|min:0',
            'date' => 'required|date',
            'buddies' => 'array',
            'buddies.*.buddy_id' => 'integer|exists:buddies,id|required_without:buddies.*.text',
            'buddies.*.text' => 'string|required_without:buddies.*.buddy_id',
            'buddies.*.color' => [new HexColor()],
            'tags' => 'array',
            'tags.*.tag_id' => 'integer|exists:tags,id|required_without:tags.*.text',
            'tags.*.text' => 'string|required_without:tags.*.tag_id',
            'tags.*.color' => [new HexColor()],
            'place.country_code' => 'string|exists:countries,iso2|required_with:place.text',
            'place.place_id' => 'integer|exists:places,id',
            'place.name' => 'string|required_with:place.country_code',
            'tanks' => 'array',
            'tanks.*.pressure.begin' => 'numeric|between:0,350',
            'tanks.*.pressure.end' => 'numeric|between:0,350',
            'tanks.*.pressure.type' => [Rule::in(['bar', 'psi'])],
            'tanks.*.volume' => 'integer',
            'tanks.*.oxygen' => 'integer|between:21,100',
            'computer_id' => 'integer|exists:computers,id',
            'fingerprint' => 'required_with:computer_id|string',
        ];
    }
}
