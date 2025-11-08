<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PropertyReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id','user_id',
        'start_date','end_date',
        'status','payment_status',
        'total_price',
    ];

    protected $casts = [
        'start_date'  => 'date',
        'end_date'    => 'date',
        'total_price' => 'float',
        'meta' => 'array',
    ];

    public function property()
    {
        return $this->belongsTo(Property::class);
    }

    // Calcula noches en tiempo real
    public function getNightsAttribute(): int
    {
        $start = $this->start_date ? Carbon::parse($this->start_date) : null;
        $end   = $this->end_date   ? Carbon::parse($this->end_date)   : null;
        if (!$start || !$end) return 1;
        return max(1, $start->diffInDays($end));
    }
}
