<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

final class EquipmentTank extends Model
{
    use HasFactory;

    protected $fillable = ['volume', 'oxygen', 'pressure_begin', 'pressure_end', 'pressure_type'];

    public function equipment()
    {
        return $this->belongsTo(Equipment::class);
    }
}
