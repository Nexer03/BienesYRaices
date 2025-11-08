<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class PropertyReservation extends Model
{
    use HasFactory;

    protected $fillable = [
        'property_id',
        'user_id',
        'start_date',
        'end_date',
        'status',
        'payment_status',
        'total_price',
        'payment_id',
        'payment_method',
        'payer_email',
        'meta',
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

    public static function overlaps(int $propertyId, $startDate, $endDate, ?int $ignoreReservationId = null): bool
    {
        $start = Carbon::parse($startDate)->startOfDay();
        $end   = Carbon::parse($endDate)->startOfDay();

        if ($start->gt($end)) {
            [$start, $end] = [$end, $start];
        }

        $startDateString = $start->toDateString();
        $endDateString   = $end->toDateString();

        return static::query()
            ->where('property_id', $propertyId)
            ->when($ignoreReservationId, function ($query, $ignoreReservationId) {
                $query->where('id', '!=', $ignoreReservationId);
            })
            ->whereNotIn('status', ['cancelled', 'canceled'])
            ->where(function ($query) use ($startDateString, $endDateString) {
                $query->whereBetween('start_date', [$startDateString, $endDateString])
                    ->orWhereBetween('end_date', [$startDateString, $endDateString])
                    ->orWhere(function ($subQuery) use ($startDateString, $endDateString) {
                        $subQuery->where('start_date', '<=', $startDateString)
                            ->where('end_date', '>=', $endDateString);
                    });
            })
            ->exists();
    }
}
