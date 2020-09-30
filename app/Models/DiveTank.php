<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DiveTank extends Model
{
    use HasFactory;

    public function dive()
    {
        return $this->belongsTo(Dive::class);
    }
}
