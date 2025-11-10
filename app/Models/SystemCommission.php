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
        'customer_percentage',
        'effective_from',
        'notes',
    ];

    protected $casts = [
        'percentage' => 'float',
        'customer_percentage' => 'float',
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
}
