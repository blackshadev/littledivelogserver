<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Littledev\Tauth\Contracts\RefreshTokenInterface;
use Littledev\Tauth\Contracts\TauthAuthenticatable;
use Ramsey\Uuid\Uuid;

/**
 * @method static Builder expired()
 * @method static Builder valid()
 * @mixin Builder
 */
class RefreshToken extends Model implements RefreshTokenInterface
{
    use HasFactory;

    public $incrementing = false;

    protected $fillable = ['user'];

    public function __toString(): string
    {
        return (string) $this->id;
    }

    public static function boot()
    {
        parent::boot();
        self::creating(function ($model) {
            $model->id = Uuid::uuid4();
        });
    }

    public function getJWTSubject()
    {
        return $this->user->id;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getJWTExtraClaims(): array
    {
        return [];
    }

    public function getToken(): string
    {
        return $this->id;
    }

    public function expire(): void
    {
        $this->expired_at = Carbon::now();
    }

    public function isExpired(): bool
    {
        return $this->expired_at !== null && $this->expired_at < Carbon::now();
    }

    public function scopeExpired(Builder $query)
    {
        return $query->where('expired_at', '<', Carbon::now());
    }

    public function scopeValid(Builder $query)
    {
        return $query->whereNull('expired_at')->orWhere('expired_at', '>=', Carbon::now());
    }

    public function setUserAttribute(TauthAuthenticatable $user)
    {
        $this->attributes['user_id'] = $user->getUserIdentifier();
    }
}
