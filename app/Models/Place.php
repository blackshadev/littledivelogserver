<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Typesense\LaravelTypesense\Interfaces\TypesenseDocument;

/**
 * @mixin Builder
 */
final class Place extends Model implements TypesenseDocument
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
            'id' => (string)$this->id,
            'name' => $this->name,
            'country_code' => $this->country?->iso2,
            'country' => $this->country?->name,
            'created_by' => (string)$this->created_by,
        ];
    }

    public function getCollectionSchema(): array
    {
        return [
            'name' => $this->searchableAs(),
            'fields' => [
                [
                    'name' => 'id',
                    'type' => 'string',
                ],
                [
                    'name' => 'name',
                    'type' => 'string',
                    'infix' => true,
                ],
                [
                    'name' => 'country',
                    'type' => 'string',
                    'optional' => true,
                ],
                [
                    'name' => 'country_code',
                    'type' => 'string',
                    'optional' => true,
                ],
                [
                    'name' => 'created_by',
                    'type' => 'string',
                    'optional' => true,
                ],
            ],
        ];
    }

    public function typesenseQueryBy(): array
    {
        return [
            'name', 'country', 'country_code'
        ];
    }
}
