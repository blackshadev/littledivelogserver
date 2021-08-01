<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use  Illuminate\Database\Eloquent\Model;

final class Equipment extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tanks()
    {
        return $this->hasMany(EquipmentTank::class);
    }
}
