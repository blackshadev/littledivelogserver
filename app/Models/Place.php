<?php

namespace App\Models;

use App\ElasticSearch\IndexConfigurators\PlaceIndexConfigurator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class Place extends Model
{
    use HasFactory;

    protected $fillable = ['country_code', 'name'];

    public function dives()
    {
        return $this->hasMany(Dive::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }
}
