<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewPropertyMatchNotification extends Notification
{
    use Queueable;

    public function __construct(public Property $property, public ?int $matchScore = null)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'property_id' => $this->property->id,
            'title' => $this->property->title,
            'property_title' => $this->property->title,
            'city' => $this->property->city,
            'price' => $this->property->price,
            'listing_type' => $this->property->listing_type,
            'match_score' => $this->matchScore,
        ];
    }
}
