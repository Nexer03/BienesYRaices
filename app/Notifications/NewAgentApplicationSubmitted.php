<?php

namespace App\Notifications;

use App\Models\AgentApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewAgentApplicationSubmitted extends Notification
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
        $applicant = $this->application->user;

        return (new MailMessage)
            ->subject('Nueva solicitud para convertirse en agente')
            ->greeting('Hola ' . ($notifiable->name ?? 'Administrador') . '!')
            ->line('Has recibido una nueva solicitud para convertirse en agente inmobiliario.')
            ->line('Solicitante: ' . ($applicant?->name ?? 'Usuario sin nombre'))
            ->action('Revisar solicitudes', route('admin.agent-applications.show', $this->application))
            ->line('Desde ahí podrás aprobarla o rechazarla.');
    }

    public function toArray(object $notifiable): array
    {
        $applicant = $this->application->user;

        return [
            'application_id' => $this->application->id,
            'applicant_id' => $applicant?->id,
            'applicant_name' => $applicant?->name,
        ];
    }
}
