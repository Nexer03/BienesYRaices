<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name','email','password','phone','avatar','bio','role'
    ];

    protected $hidden = ['password','remember_token'];

    // Relaciones
    public function properties()
    {
        return $this->hasMany(Property::class);
    }
    public function preferences()
{
    // Define la relación uno a uno con UserPreference
    return $this->hasOne(UserPreference::class);
}

    public function favorites()
    {
        return $this->belongsToMany(Property::class, 'favorites');
    }

    public function visitsAsClient()
    {
        return $this->hasMany(Visit::class, 'client_id');
    }

    public function visitsAsAgent()
    {
        return $this->hasMany(Visit::class, 'agent_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }
}
