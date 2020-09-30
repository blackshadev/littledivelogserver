<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class Country extends Model
{
    use HasFactory;

    protected $fillable = ['iso2'];

    protected $primaryKey = 'iso2';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    public function places()
    {
        return $this->hasMany(Place::class);
    }

}
