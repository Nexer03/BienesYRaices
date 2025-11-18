<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Visit extends Model
{
    protected $fillable = [
        'client_id', 'agent_id', 'property_id', 'visit_date', 'status', 'notes',
    ];

    protected $casts = [
        'visit_date' => 'datetime',
    ];

    public function property(): BelongsTo
    {
        return $this->belongsTo(Property::class);
    }

    public function agent(): BelongsTo
    {
        return $this->belongsTo(User::class, 'agent_id');
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(User::class, 'client_id');
    }

    public static function overlapsForAgent(int $agentId, Carbon|string $visitDate, ?int $ignoreVisitId = null, int $durationMinutes = 60): bool
    {
        $start = $visitDate instanceof Carbon ? $visitDate->copy() : Carbon::parse($visitDate);
        $end   = (clone $start)->addMinutes($durationMinutes);

        return static::where('agent_id', $agentId)
            ->when($ignoreVisitId, fn($q) => $q->where('id', '!=', $ignoreVisitId))
            ->where('visit_date', '<', $end)
            ->where('visit_date', '>=', $start->copy()->subMinutes($durationMinutes))
            ->exists();
    }
    
}
