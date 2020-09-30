<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Dive extends Model
{
    use HasFactory;

    protected $dates = ['created_at', 'updated_at', 'date'];

    public function buddies()
    {
        return $this->belongsToMany(Buddy::class);
    }

    public function tags()
    {
        return $this->belongsToMany(Tag::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function tanks()
    {
        return $this->hasMany(DiveTank::class);
    }

    public function place()
    {
        return $this->belongsTo(Place::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function computer()
    {
        return $this->belongsTo(Computer::class);
    }
}
