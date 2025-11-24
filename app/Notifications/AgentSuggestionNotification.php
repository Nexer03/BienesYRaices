<?php

namespace App\Notifications;

use App\Models\AgentSuggestion;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgentSuggestionNotification extends Notification
{
    use Queueable;

    public function __construct(public AgentSuggestion $suggestion)
    {
        $this->suggestion->loadMissing(['property', 'user']);
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $suggestion = $this->suggestion;
        $property = $suggestion->property;

        $message = (new MailMessage)
            ->subject('Nueva sugerencia de un cliente')
            ->greeting('Hola ' . ($notifiable->name ?? ''))
            ->line('Has recibido una sugerencia directa de un cliente.')
            ->line('Propiedad: ' . ($property?->title ?? ''))
            ->line('Tipo: ' . ($suggestion->type === 'visit' ? 'Visita' : 'Renta'))
            ->line('Cliente: ' . ($suggestion->user?->name ?? ''))
            ->line('Mensaje:')
            ->line($suggestion->message)
            ->action('Ver sugerencias', route('agent.suggestions.index'))
            ->line('Gracias por mejorar la experiencia de tus clientes.');

        return $message;
    }

    public function toArray(object $notifiable): array
    {
        $suggestion = $this->suggestion;
        $property = $suggestion->property;

        return [
            'property_title' => $property?->title,
            'property_id' => $property?->id,
            'type' => $suggestion->type,
            'client_name' => $suggestion->user?->name,
            'suggestion_id' => $suggestion->id,
        ];
    }
}
