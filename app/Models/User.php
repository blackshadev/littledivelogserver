<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as AuthUser;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Littledev\Tauth\Contracts\TauthAuthenticatable;

/**
 * @mixin Builder
 */
class User extends AuthUser implements TauthAuthenticatable
{
    use Notifiable, HasFactory;

    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sessions()
    {
        return $this->hasMany(RefreshToken::class);
    }

    public function setPasswordAttribute(string $password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function getUserIdentifier(): int
    {
        return $this->id;
    }

    public function dives()
    {
        return $this->hasMany(Dive::class);
    }

    public function buddies()
    {
        return $this->hasMany(Buddy::class);
    }

    public function tags()
    {
        return $this->hasMany(Tag::class);
    }

    public function computers()
    {
        return $this->hasMany(Computer::class);
    }

    public function equipment()
    {
        return $this->hasOne(Equipment::class);
    }
}
