<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 */
class Tag extends Model
{
    use HasFactory;

    protected $fillable = ['text', 'color'];

    public function dives()
    {
        return $this->belongsToMany(Dive::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
