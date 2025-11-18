<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
        protected $fillable = [
        'property_id',
        'agent_id',
        'client_id',
        'visit_id',
        'sale_price',
        'commission_percentage',
        'commission_amount',
        'notes',
        'commission_paid_at',
    ];


    public function property() { return $this->belongsTo(Property::class); }
    public function agent()    { return $this->belongsTo(User::class, 'agent_id'); }
    public function client()   { return $this->belongsTo(User::class, 'client_id'); }
    public function visit()    { return $this->belongsTo(Visit::class); }
}
