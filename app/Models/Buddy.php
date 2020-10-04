<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class Buddy extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'color'];

    public function dives()
    {
        return $this->belongsToMany(Dive::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
