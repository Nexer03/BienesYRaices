<?php

namespace App\Services;

use App\Models\AlertChannelPreference;
use App\Models\AlertCriteria;
use App\Models\AlertDeliveryLog;
use App\Models\Property;
use App\Notifications\AlertDigestNotification;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;

class AlertDispatchService
{
    public function __construct(private readonly AlertMatchingService $matcher)
    {
    }

    public function handlePropertyEvent(Property $property, string $trigger = 'event'): void
    {
        $criteriaList = AlertCriteria::with('channelPreferences')
            ->where('is_paused', false)
            ->get()
            ->filter(fn (AlertCriteria $criteria) => $criteria->matchesProperty($property));

        foreach ($criteriaList as $criteria) {
            $channels = $criteria->channelPreferences->where('frequency', 'immediate')->filter->isSubscribed();
            foreach ($channels as $channel) {
                $this->dispatchDigest($criteria, new Collection([$property]), $channel, $trigger);
            }
        }
    }

    public function dispatchScheduled(string $frequency): void
    {
        $criteriaList = AlertCriteria::with('channelPreferences')
            ->where('is_paused', false)
            ->where(function ($q) use ($frequency) {
                $q->where('frequency', $frequency)
                    ->orWhereHas('channelPreferences', function ($query) use ($frequency) {
                        $query->where('frequency', $frequency)->where('enabled', true);
                    });
            })
            ->get();

        foreach ($criteriaList as $criteria) {
            foreach ($criteria->channelPreferences as $channel) {
                if ($channel->frequency !== $frequency) {
                    continue;
                }

                $lastSent = $channel->last_sent_at;
                $properties = $this->matcher->findNewProperties($criteria, $lastSent);
                $this->dispatchDigest($criteria, $properties, $channel, $frequency);
            }
        }
    }

    public function dispatchDigest(AlertCriteria $criteria, Collection $properties, AlertChannelPreference $channel, string $trigger): void
    {
        if ($properties->isEmpty()) {
            return;
        }

        if (! $this->matcher->canSendOnChannel($channel)) {
            return;
        }

        $hash = $this->matcher->dedupHash($criteria, $channel, $properties);
        $alreadySent = AlertDeliveryLog::where('dedup_hash', $hash)->exists();

        if ($alreadySent) {
            return;
        }

        $sentAt = now();

        try {
            $criteria->user->notify(new AlertDigestNotification($criteria, $properties, $channel->channel));

            $channel->forceFill(['last_sent_at' => $sentAt])->save();
            $criteria->forceFill([
                'last_sent_at' => $sentAt,
                'last_matched_at' => $sentAt,
            ])->save();

            AlertDeliveryLog::create([
                'alert_criteria_id' => $criteria->id,
                'channel' => $channel->channel,
                'status' => 'delivered',
                'sent_at' => $sentAt,
                'properties_count' => $properties->count(),
                'frequency' => $channel->frequency,
                'dedup_hash' => $hash,
                'error_message' => null,
            ]);
        } catch (\Throwable $e) {
            Log::error('Error al enviar alerta', [
                'criteria_id' => $criteria->id,
                'channel' => $channel->channel,
                'error' => $e->getMessage(),
            ]);

            AlertDeliveryLog::create([
                'alert_criteria_id' => $criteria->id,
                'channel' => $channel->channel,
                'status' => 'failed',
                'sent_at' => $sentAt,
                'properties_count' => $properties->count(),
                'frequency' => $channel->frequency,
                'dedup_hash' => $hash,
                'error_message' => $e->getMessage(),
            ]);
        }
    }
}
