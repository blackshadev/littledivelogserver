<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JeroenG\Explorer\Application\Aliased;
use JeroenG\Explorer\Application\Explored;
use Laravel\Scout\Searchable;

/**
 * @mixin Builder
 */
final class Place extends Model implements Explored, Aliased
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['country_code', 'name'];

    public function dives()
    {
        return $this->hasMany(Dive::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_code', 'iso2');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'country_code' => $this->country !== null ? $this->country->iso2 : null,
            'country' => $this->country !== null ? $this->country->name : null,
            'created_by' => $this->created_by,
        ];
    }

    public function mappableAs(): array
    {
        return [
            'id' => 'keyword',
            'name' => 'text',
            'country_code' => 'keyword',
            'country' => 'text',
            'created_by' => 'keyword',
        ];
    }
}
