<?php

namespace App\Models;

use App\ElasticSearch\IndexConfigurators\PlaceIndexConfigurator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use ScoutElastic\Searchable;

/**
 * @mixin Builder
 */
class Place extends Model
{
    use HasFactory, Searchable;

    protected $indexConfigurator = PlaceIndexConfigurator::class;

    protected $searchRules = [
        //
    ];

    // Here you can specify a mapping for model fields
    protected $mapping = [
        'properties' => [
            'name' => [
                'type' => 'text',
            ],
            'country_code' => [
                'type' => 'text',
            ],
        ]
    ];

    public function dives()
    {
        return $this->hasMany(Dive::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
