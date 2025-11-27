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
        return $this->hasOne(UserPreference::class);
    }

    public function favoriteProperties()
    {
        return $this->belongsToMany(\App\Models\Property::class, 'favorites')->withTimestamps();
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

    public function alertCriteria()
    {
        return $this->hasMany(AlertCriteria::class);
    }

    public function agentVisits()
    {
        return $this->hasMany(Visit::class, 'agent_id');
    }

    public function clientVisits()
    {
        return $this->hasMany(Visit::class, 'client_id');
    }

    // Traducción de roles
    public function getRoleLabelAttribute()
    {
        $map = [
            'admin'          => 'Administrador',
            'superadmin'     => 'Super Administrador',
            'agent'          => 'Agente',
            'client'         => 'Cliente',
            'pending_agent'  => 'Solicitante a Agente',
            'banned'         => 'Suspendido',
        ];

        return $map[$this->role] ?? ucfirst($this->role);
    }
}
