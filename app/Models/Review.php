<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Review extends Model
{
    protected $fillable = [
        'property_id','reservation_id','author_id','agent_id',
        'cleanliness','accuracy','communication','location','value','checkin',
        'overall','comment','is_public','published_at',
    ];

    // Relaciones
    public function property(){ return $this->belongsTo(Property::class); }
    public function reservation(){ return $this->belongsTo(PropertyReservation::class, 'reservation_id'); }
    public function author(){ return $this->belongsTo(User::class, 'author_id'); }
    public function agent(){ return $this->belongsTo(User::class, 'agent_id'); }

    // Scopes
    public function scopePublic($q){ return $q->where('is_public', true); }
}
