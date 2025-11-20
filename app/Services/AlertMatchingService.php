<?php

namespace App\Services;

use App\Models\AlertChannelPreference;
use App\Models\AlertCriteria;
use App\Models\Property;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;

/**
 * Motor de coincidencia y reglas de deduplicación/antispam.
 *
 * Reglas principales:
 *  - El último envío por canal se guarda en alert_channel_preferences.last_sent_at.
 *  - Si hay un envío en la ventana de cooldown (cooldown_minutes), se omite para evitar spam.
 *  - Se calcula un hash de deduplicación por criterio+canal+IDs de propiedad para evitar repeticiones.
 *  - Se consulta el histórico (alert_delivery_logs) en una ventana antispam (antispam_window_minutes)
 *    para limitar el número de notificaciones inmediatas.
 */
class AlertMatchingService
{
    public function findNewProperties(AlertCriteria $criteria, ?Carbon $since = null): Collection
    {
        $since = $since ?? $criteria->last_sent_at ?? $criteria->created_at;
        $filters = $criteria->filters ?? [];

        $query = Property::query()->available();

        if ($since) {
            $query->where(function ($q) use ($since) {
                $q->where('created_at', '>', $since)
                    ->orWhere('updated_at', '>', $since);
            });
        }

        if (!empty($filters['city'])) {
            $query->where('city', $filters['city']);
        }

        if (!empty($filters['listing_type'])) {
            $query->where('listing_type', $filters['listing_type']);
        }

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['price_min'])) {
            $query->where('price', '>=', (float) $filters['price_min']);
        }

        if (!empty($filters['price_max'])) {
            $query->where('price', '<=', (float) $filters['price_max']);
        }

        if (!empty($filters['bedrooms'])) {
            $query->where('bedrooms', '>=', (int) $filters['bedrooms']);
        }

        if (!empty($filters['bathrooms'])) {
            $query->where('bathrooms', '>=', (int) $filters['bathrooms']);
        }

        return $query->latest()->get();
    }

    public function canSendOnChannel(AlertChannelPreference $channel): bool
    {
        if (! $channel->isSubscribed()) {
            return false;
        }

        if ($channel->last_sent_at && $channel->last_sent_at->gt(now()->subMinutes($channel->cooldown_minutes))) {
            return false;
        }

        $recentCount = $channel->criteria
            ->deliveryLogs()
            ->where('channel', $channel->channel)
            ->where('sent_at', '>=', now()->subMinutes($channel->antispam_window_minutes))
            ->count();

        return $recentCount < 3;
    }

    public function dedupHash(AlertCriteria $criteria, AlertChannelPreference $channel, Collection $properties): string
    {
        return sha1($criteria->id . '|' . $channel->channel . '|' . $properties->pluck('id')->sort()->implode(','));
    }
}
