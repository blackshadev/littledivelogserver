<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\CarbonInterface;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @mixin Builder
 * @property integer $id
 * @property string $vendor
 * @property string $name
 * @property int $model
 * @property int $type
 * @property string $last_fingerprint
 * @property CarbonInterface $last_read
 */
final class Computer extends Model
{
    use HasFactory;

    protected $fillable = ['vendor', 'model', 'name', 'serial', 'type', 'last_fingerprint', 'last_read'];

    protected $casts = [
        'last_read' => 'datetime'
    ];

    public function dives()
    {
        return $this->hasMany(Dive::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
