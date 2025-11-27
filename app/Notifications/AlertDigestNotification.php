<?php

namespace App\Notifications;

use App\Models\AlertCriteria;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Str;
use Illuminate\Support\Collection;

class AlertDigestNotification extends Notification
{
    use Queueable;

    public function __construct(
        public AlertCriteria $criteria,
        public Collection $properties,
        public string $channel,
    ) {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        $properties = $this->properties
            ->take(5)
            ->map(fn ($property) => [
                'id' => $property->id,
                'title' => Str::limit($property->title, 80),
                'city' => $property->city,
                'price' => $property->price,
                'listing_type' => $property->listing_type,
            ])
            ->values();

        return [
            'criteria_id' => $this->criteria->id,
            'criteria_name' => $this->criteria->name,
            'channel' => $this->channel,
            'properties' => $properties,
            'properties_count' => $this->properties->count(),
            'primary_property_id' => $properties->first()['id'] ?? null,
        ];
    }
}
