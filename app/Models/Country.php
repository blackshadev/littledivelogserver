<?php

declare(strict_types=1);

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

    public $incrementing = false;

    public $timestamps = false;

    protected $fillable = ['iso2'];

    protected $primaryKey = 'iso2';

    protected $keyType = 'string';

    public function places()
    {
        return $this->hasMany(Place::class, 'iso2', 'country_code');
    }
}
