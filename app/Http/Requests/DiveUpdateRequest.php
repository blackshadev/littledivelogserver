<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DiveUpdateRequest extends FormRequest
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
            'buddies.*.color' => 'regex:/^\#[0-9abcdef]{6}$/i',
            'tags' => 'array',
            'tags.*.tag_id' => 'integer|exists:tags,id|required_without:tags.*.text',
            'tags.*.text' => 'string|required_without:tags.*.tag_id',
            'tags.*.color' => 'regex:/^\#[0-9abcdef]{6}$/i',
            'place.country_code' => 'string|exists:countries,iso2|required_with:place.text',
            'place.place_id' => 'integer|exists:places,id|required_without:place.text',
            'place.name' => 'string|required_without:place.place_id',
            'tanks' => 'array',
            'tanks.*.pressure.begin' => 'integer|between:0,350',
            'tanks.*.pressure.end' => 'integer|between:0,350',
            'tanks.*.pressure.type' => Rule::in(['bar', 'psi']),
            'tanks.*.volume' => 'integer',
            'tanks.*.oxygen' => 'integer|between:21,100'
        ];
    }
}
