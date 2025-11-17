<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SystemCommission extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'listing_type',
        'percentage',
        'effective_from',
        'notes',
    ];

    protected $casts = [
        'percentage' => 'float',
        'effective_from' => 'date',
    ];

    public function agent()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopeForListingType($query, string $listingType)
    {
        return $query->where(function ($q) use ($listingType) {
            $q->where('listing_type', $listingType)
              ->orWhere('listing_type', 'both');
        });
    }

    /**
     * Obtiene la comisión correcta para un tipo de listado y agente.
     * Prioriza la comisión del agente; si no hay, usa la general.
     */
    public static function getCommissionFor($listingType, $agentId = null)
    {
        // 1. Comisión específica del agente
        $agentCommission = static::query()
            ->where('user_id', $agentId)
            ->whereIn('listing_type', [$listingType, 'both'])
            ->orderByDesc('effective_from')
            ->first();

        if ($agentCommission) {
            return $agentCommission;
        }

        // 2. Comisión general del sistema
        return static::query()
            ->whereNull('user_id')
            ->whereIn('listing_type', [$listingType, 'both'])
            ->orderByDesc('effective_from')
            ->first();
    }
}
