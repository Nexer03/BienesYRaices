<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use App\Models\Property;

class AlertCriteria extends Model
{
    use HasFactory;

    protected $table = 'alert_criteria';

    protected $fillable = [
        'user_id',
        'name',
        'filters',
        'frequency',
        'is_paused',
        'consented_at',
        'last_sent_at',
        'last_matched_at',
        'last_consent_refresh_at',
        'last_unsubscribe_at',
    ];

    protected $casts = [
        'filters' => 'array',
        'is_paused' => 'boolean',
        'consented_at' => 'datetime',
        'last_sent_at' => 'datetime',
        'last_matched_at' => 'datetime',
        'last_consent_refresh_at' => 'datetime',
        'last_unsubscribe_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function channelPreferences(): HasMany
    {
        return $this->hasMany(AlertChannelPreference::class);
    }

    public function deliveryLogs(): HasMany
    {
        return $this->hasMany(AlertDeliveryLog::class);
    }

    public function matchesProperty(Property $property): bool
    {
        $filters = $this->filters ?? [];

        if ($this->is_paused) {
            return false;
        }

        $checks = [
            fn () => !Arr::get($filters, 'city') || $property->city === Arr::get($filters, 'city'),
            fn () => !Arr::get($filters, 'listing_type') || $property->listing_type === Arr::get($filters, 'listing_type'),
            fn () => !Arr::get($filters, 'type') || $property->type === Arr::get($filters, 'type'),
            fn () => !Arr::get($filters, 'price_min') || $property->price >= (float) Arr::get($filters, 'price_min'),
            fn () => !Arr::get($filters, 'price_max') || $property->price <= (float) Arr::get($filters, 'price_max'),
            fn () => !Arr::get($filters, 'bedrooms') || $property->bedrooms >= (int) Arr::get($filters, 'bedrooms'),
            fn () => !Arr::get($filters, 'bathrooms') || $property->bathrooms >= (int) Arr::get($filters, 'bathrooms'),
        ];

        foreach ($checks as $check) {
            if (! $check()) {
                return false;
            }
        }

        return true;
    }
}
