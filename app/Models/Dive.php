<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use JeroenG\Explorer\Application\Explored;
use Laravel\Scout\Searchable;

class Dive extends Model implements Explored
{
    use HasFactory;
    use Searchable;

    protected $fillable = ['date', 'max_depth', 'divetime'];

    protected $dates = ['created_at', 'updated_at', 'date'];

    protected $casts = [
        'max_depth' => 'float'
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

    public function getCountryCodeAttribute(): ?string
    {
        return $this->place !== null ? $this->place->country_code : $this->attributes['country_code'] ?? null;
    }

    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'max_depth' => $this->max_depth,
            'date' => $this->date,
            'created_at' => $this->created_at,
            'tags' => $this->tags()->getQuery()->select(['tags.id', 'tags.text'])->get()->toArray(),
            'buddies' => $this->buddies()->getQuery()->select(['buddies.id', 'buddies.name'])->get()->toArray(),
            'place' => $this->place !== null ? [
                'id' => $this->place->id,
                'name' => $this->place->name,
                'country_code' => $this->place->country_code,
            ] : null
        ];
    }

    public function mappableAs(): array
    {
        return [
            'id' => 'keyword',
            'user_id' => 'keyword',
            'max_depth' => 'float',
            'date' => 'date',
            'created_at' => 'date',
            'tags' => [
                'type' => 'nested',
                'properties' => [
                    'id' => 'keyword',
                    'text' => 'text'
                ]
            ],
            'buddies' => [
                'type' => 'nested',
                'properties' => [
                    'id' => 'keyword',
                    'name' => 'text'
                ]
            ],
            'place' => [
                'type' => 'nested',
                'properties' => [
                    'id' => 'keyword',
                    'name' => 'text',
                    'country_code' => 'keyword'
                ]
            ],
        ];
    }
}
