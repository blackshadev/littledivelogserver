<?php

declare(strict_types=1);

namespace App\Models;

use App\Domain\Dives\ValueObjects\DiveId;
use App\Domain\DiveSamples\Entities\DiveSamples;
use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Laravel\Scout\Searchable;
use Typesense\LaravelTypesense\Interfaces\TypesenseDocument;

/**
 * @mixin Builder
 *
 * @property int $id
 * @property int $user_id
 * @property int $divetime
 * @property float $max_depth
 * @property int $place_id
 * @property string $country_code
 * @property CarbonInterface $date
 * @property CarbonInterface $created_at
 * @property CarbonInterface $updated_at
 */
final class Dive extends Model implements TypesenseDocument
{
    use HasFactory;
    use Searchable;

    public const DIVE_COLUMNS = [
        'id', 'created_at', 'updated_at', 'user_id', 'date', 'divetime', 'max_depth', 'country_code', 'place_id',
        'computer_id', 'fingerprint'
    ];

    protected $fillable = ['date', 'max_depth', 'divetime'];

    protected $casts = [
        'date' => 'datetime',
        'max_depth' => 'float',
        'samples' => 'array',
    ];

    protected $hidden = [
        'samples'
    ];

    public function buddies()
    {
        return $this->belongsToMany(Buddy::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tanks()
    {
        return $this->hasMany(DiveTank::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function computer()
    {
        return $this->belongsTo(Computer::class);
    }

    public function diveSamples(): DiveSamples
    {
        return DiveSamples::create(DiveId::existing($this->id), $this->samples);
    }

    public function getCountryCodeAttribute(): ?string
    {
        return $this->place !== null ? $this->place->country_code : $this->attributes['country_code'] ?? null;
    }

    public function toSearchableArray(): array
    {
        $place = $this->place;

        return [
            'id' => (string)$this->id,
            'user_id' => (string)$this->user_id,
            'max_depth' => $this->max_depth,
            'divetime' => $this->divetime,
            'date' => $this->date->timestamp,
            'created_at' => $this->created_at->timestamp,
            'tags.id' => $this->tags()->getQuery()
                ->pluck('tags.id')
                ->map(static fn (int $id) => (string)$id)
                ->toArray(),
            'tags.name' => $this->tags()->getQuery()->pluck('tags.text')->toArray(),
            'buddies.id' => $this->buddies()->getQuery()
                ->pluck('buddies.id')
                ->map(static fn (int $id) => (string)$id)
                ->toArray(),
            'buddies.name' => $this->buddies()->getQuery()->pluck('buddies.name')->toArray(),
            'place.id' => (string)$place?->id,
            'place.name' => $place?->name,
            'place.country_code' => $place?->country_code,
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
                    'name' => 'user_id',
                    'type' => 'string',
                ],
                [
                    'name' => 'created_at',
                    'type' => 'int64',
                ],
                [
                    'name' => 'max_depth',
                    'type' => 'float',
                    'optional' => true,
                ],
                [
                    'name' => 'divetime',
                    'type' => 'int64',
                    'optional' => true,
                ],
                [
                    'name' => 'date',
                    'type' => 'int64',
                    'optional' => true,
                ],
                [
                    'name' => 'tags.id',
                    'type' => 'string[]',
                ],
                [
                    'name' => 'tags.name',
                    'type' => 'string[]',
                ],
                [
                    'name' => 'buddies.name',
                    'type' => 'string[]',
                ],

                [
                    'name' => 'buddies.id',
                    'type' => 'string[]',
                ],
                [
                    'name' => 'place.id',
                    'type' => 'string',
                    'optional' => true,
                ],
                [
                    'name' => 'place.name',
                    'type' => 'string',
                    'optional' => true,
                ],
                [
                    'name' => 'place.country_code',
                    'type' => 'string',
                    'optional' => true,
                ],
            ],
        ];
    }

    public function typesenseQueryBy(): array
    {
        return [
            'buddies.name',
            'tags.name',
            'place.name',
        ];
    }
}
