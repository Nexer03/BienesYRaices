<?php

namespace App\Notifications;

use App\Models\AgentApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AgentApplicationRejected extends Notification
{
    use Queueable;

    public function __construct(public AgentApplication $application)
    {
        $this->application->loadMissing('user');
    }

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Tu solicitud para ser agente ha sido rechazada')
            ->greeting('Hola ' . ($notifiable->name ?? '') . '!')
            ->line('Gracias por tu interés en convertirte en agente inmobiliario.')
            ->line('Tras revisar tu solicitud, ha sido rechazada.')
            ->line('Motivo del rechazo:')
            ->line($this->application->rejection_reason)
            ->line('Si consideras que se trata de un error o deseas más información, responde a este correo.');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'application_id' => $this->application->id,
            'status' => $this->application->status,
            'rejection_reason' => $this->application->rejection_reason,
        ];
    }
}
