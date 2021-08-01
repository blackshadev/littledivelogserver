<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
final class Computer extends Model
{
    use HasFactory;

    protected $fillable = ['vendor', 'model', 'name', 'serial', 'type', 'last_fingerprint', 'last_read'];

    protected $dates = [Model::CREATED_AT, Model::UPDATED_AT, 'last_read'];

    public function dives()
    {
        return $this->hasMany(Dive::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
