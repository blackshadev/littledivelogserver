<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use HasFactory;

    public function dives()
    {
        return $this->belongsToMany(Dive::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
