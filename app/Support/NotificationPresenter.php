<?php

namespace App\Support;

use App\Notifications\NewAgentApplicationSubmitted;
use App\Notifications\NewMessageNotification;
use App\Notifications\NewPropertyMatchNotification;
use App\Notifications\ReservationPaidNotification;
use App\Models\User;
use Illuminate\Notifications\DatabaseNotification;

class NotificationPresenter
{
    public function summarize(DatabaseNotification $notification, User $user): array
    {
        return [
            'id' => $notification->id,
            'title' => $this->title($notification),
            'description' => $this->description($notification),
            'icon' => $this->icon($notification),
            'url' => $this->url($notification, $user),
            'read' => $notification->read_at !== null,
            'time' => optional($notification->created_at)->diffForHumans(),
        ];
    }

    public function url(DatabaseNotification $notification, User $user): string
    {
        $data = $notification->data ?? [];

        return match ($notification->type) {
            NewMessageNotification::class => $this->messageUrl($data),
            ReservationPaidNotification::class => $this->reservationUrl($user),
            NewAgentApplicationSubmitted::class => route('admin.agent-applications.index'),
            NewPropertyMatchNotification::class => $this->propertyUrl($data),
            default => route('dashboard'),
        };
    }

    protected function title(DatabaseNotification $notification): string
    {
        return match ($notification->type) {
            NewMessageNotification::class => 'Nuevo mensaje recibido',
            ReservationPaidNotification::class => 'Reserva confirmada',
            NewAgentApplicationSubmitted::class => 'Nueva solicitud de agente',
            NewPropertyMatchNotification::class => 'Nueva propiedad recomendada',
            default => 'Notificación',
        };
    }

    protected function description(DatabaseNotification $notification): string
    {
        $data = $notification->data ?? [];

        return match ($notification->type) {
            NewMessageNotification::class => trim(($data['sender_name'] ?? 'Un usuario') . ' te ha escrito.'),
            ReservationPaidNotification::class => 'Se registró un pago para ' . ($data['property_title'] ?? 'una propiedad'),
            NewAgentApplicationSubmitted::class => ($data['applicant_name'] ?? 'Un usuario') . ' desea convertirse en agente.',
            NewPropertyMatchNotification::class => $this->propertyDescription($data),
            default => 'Tienes novedades en la plataforma.',
        };
    }

    protected function icon(DatabaseNotification $notification): string
    {
        return match ($notification->type) {
            NewMessageNotification::class => 'fa-regular fa-message',
            ReservationPaidNotification::class => 'fa-solid fa-receipt',
            NewAgentApplicationSubmitted::class => 'fa-solid fa-user-tie',
            NewPropertyMatchNotification::class => 'fa-solid fa-house-circle-check',
            default => 'fa-regular fa-bell',
        };
    }

    protected function messageUrl(array $data): string
    {
        if (!empty($data['conversation_id'])) {
            return route('chat.open', $data['conversation_id']);
        }

        return route('chat.index');
    }

    protected function reservationUrl(User $user): string
    {
        if ($user->role === 'agent') {
            return route('agent.reservations.index');
        }

        return route('admin.reports.sales');
    }

    protected function propertyUrl(array $data): string
    {
        if (!empty($data['property_id'])) {
            return route('properties.show', $data['property_id']);
        }

        return route('dashboard');
    }

    protected function propertyDescription(array $data): string
    {
        $parts = [];

        $title = $data['property_title'] ?? $data['title'] ?? null;
        $city = $data['city'] ?? null;
        $price = $data['price'] ?? null;

        $parts[] = $title
            ? "Hemos encontrado una propiedad que podría interesarte: \"{$title}\""
            : 'Hemos encontrado una propiedad que podría interesarte';

        if ($city) {
            $parts[] = "en {$city}";
        }

        if ($price) {
            $parts[] = 'con un precio de $' . number_format((float) $price, 2);
        }

        return implode(' ', $parts) . '.';
    }
}
