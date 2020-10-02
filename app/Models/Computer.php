<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class Computer extends Model
{
    use HasFactory;

    public function dives()
    {
        return $this->hasMany(Dive::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
