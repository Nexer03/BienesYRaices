<?php

namespace App\Notifications;

use App\Models\Property;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPropertyMatchNotification extends Notification
{
    use Queueable;

    public function __construct(public Property $property)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Nueva propiedad que coincide contigo')
            ->greeting('Hola ' . $notifiable->name . '!')
            ->line('Hemos publicado una nueva propiedad que coincide con tus preferencias guardadas.')
            ->line('Propiedad: ' . $this->property->title)
            ->line('Ubicación: ' . $this->property->city)
            ->line('Precio: $' . number_format($this->property->price, 2, '.', ','))
            ->action('Ver propiedad', route('properties.show', $this->property))
            ->line('Inicia sesión para conocer todos los detalles.');
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
        ];
    }
}
