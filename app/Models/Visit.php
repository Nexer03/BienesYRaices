<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Visit extends Model
{
    protected $fillable = [
        'client_id',
        'agent_id', 
        'property_id',
        'visit_date',
        'status',
        'notes'
    ];
    
    protected $casts = [
        'visit_date' => 'datetime',
    ];
    // Relación con el cliente (usuario que agenda la visita)
    public function client()
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    // Relación con el agente (dueño de la propiedad)
    public function agent()
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    // Relación con la propiedad
    public function property()
    {
        return $this->belongsTo(Property::class);
    }
}