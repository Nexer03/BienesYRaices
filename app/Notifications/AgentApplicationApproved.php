<?php

namespace App\Notifications;

use App\Models\AgentApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgentApplicationApproved extends Notification
{
    use Queueable;

    public function __construct(public AgentApplication $application)
    {
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('¡Tu solicitud para ser agente ha sido aprobada!')
            ->greeting('Hola ' . $notifiable->name . '!')
            ->line('Tu solicitud para convertirte en agente inmobiliario fue aprobada por nuestro equipo.')
            ->line('Ahora puedes acceder al panel de agentes y comenzar a publicar tus propiedades.')
            ->action('Ir al panel de agentes', route('agent.home'))
            ->line('Gracias por formar parte de nuestra comunidad.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'status' => $this->application->status,
        ];
    }
}
