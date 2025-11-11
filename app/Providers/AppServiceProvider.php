<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Notifications\ReservationPaidNotification;
use App\Notifications\NewAgentApplicationSubmitted;
use App\Notifications\AgentApplicationApproved;
use App\Notifications\NewMessageNotification;
use App\Notifications\NewPropertyMatchNotification;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    protected $policies = [
        \App\Models\Visit::class => \App\Policies\VisitPolicy::class,
        \App\Models\Property::class => \App\Policies\PropertyPolicy::class,
    ];

    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Tu Gate de admin
        Gate::before(function ($user, $ability) {
            return $user->role === 'admin' ? true : null;
        });

        Schema::defaultStringLength(191);

        /*
         * ====== Composer global para notificaciones en el header ======
         * Esto hace que $unreadNotificationCount y $headerNotifications
         * estén disponibles en TODAS las vistas (welcome, dashboard, admin, etc.)
         */
        View::composer('*', function ($view) {
    if (!Auth::check()) {
        return;
    }

    $user = Auth::user();

    // Últimas 10 notificaciones
    $notifications = $user->notifications()
        ->orderByDesc('created_at')
        ->limit(10)
        ->get();

    $unreadNotificationCount = $user->unreadNotifications()->count();

    $headerNotifications = $notifications->map(function ($notification) {
        $data = $notification->data ?? [];
        $type = $notification->type;

        // 1) Intento genérico: si la notificación ya trae title/description,
        //    los respetamos.
        $title =
            $data['title']
            ?? $data['subject']
            ?? $data['heading']
            ?? $data['message']
            ?? $data['body']
            ?? null;

        $description =
            $data['description']
            ?? $data['message']
            ?? $data['body']
            ?? null;

        /*
         * 2) Casos específicos por tipo de notificación
         *    (si aún no hay título/descripción, los construimos).
         */

        // 2.a) Reserva pagada
        if ($type === ReservationPaidNotification::class) {
            $propertyTitle = $data['property_title'] ?? 'una propiedad';
            $startDate     = $data['start_date']      ?? null;
            $endDate       = $data['end_date']        ?? null;
            $totalPrice    = $data['total_price']     ?? null;
            $paymentMethod = $data['payment_method']  ?? null;

            if (!$title) {
                $title = 'Reserva pagada';
            }

            if (!$description) {
                $parts = [];

                $parts[] = "Se ha confirmado el pago de la reserva de \"{$propertyTitle}\"";

                if ($startDate && $endDate) {
                    $parts[] = "del {$startDate} al {$endDate}";
                }

                if ($totalPrice) {
                    $parts[] = "por $" . number_format($totalPrice, 2);
                }

                if ($paymentMethod) {
                    $parts[] = "vía " . strtoupper($paymentMethod);
                }

                $description = implode(' ', $parts) . '.';
            }
        }

        // 2.b) Nueva solicitud de agente (para admin)
        if ($type === NewAgentApplicationSubmitted::class) {
            $applicantName = $data['applicant_name'] ?? $data['user_name'] ?? null;

            if (!$title) {
                $title = 'Nueva solicitud de agente';
            }

            if (!$description) {
                if ($applicantName) {
                    $description = "{$applicantName} desea convertirse en agente.";
                } else {
                    $description = 'Un usuario ha enviado una solicitud para convertirse en agente.';
                }
            }
        }

        // 2.c) Solicitud de agente aprobada (para el usuario)
        if ($type === AgentApplicationApproved::class) {
            if (!$title) {
                $title = 'Solicitud de agente aprobada';
            }

            if (!$description) {
                $description = 'Tu solicitud para convertirte en agente ha sido aprobada. Ya puedes acceder al panel de agente.';
            }
        }

        // 2.d) Nuevo mensaje en el chat
        if ($type === NewMessageNotification::class) {
            $senderName    = $data['sender_name']    ?? $data['from_name'] ?? null;
            $propertyTitle = $data['property_title'] ?? null;

            if (!$title) {
                $title = 'Nuevo mensaje';
            }

            if (!$description) {
                $pieces = [];

                if ($senderName) {
                    $pieces[] = "Has recibido un nuevo mensaje de {$senderName}";
                } else {
                    $pieces[] = 'Has recibido un nuevo mensaje';
                }

                if ($propertyTitle) {
                    $pieces[] = "sobre la propiedad \"{$propertyTitle}\"";
                }

                $description = implode(' ', $pieces) . '.';
            }
        }

        // 2.e) Nueva propiedad recomendada para el usuario
        if ($type === NewPropertyMatchNotification::class) {
            $propertyTitle = $data['property_title'] ?? null;
            $city          = $data['city']           ?? null;
            $price         = $data['price']          ?? null;

            if (!$title) {
                $title = 'Nueva propiedad recomendada';
            }

            if (!$description) {
                $parts = [];

                if ($propertyTitle) {
                    $parts[] = "Hemos encontrado una propiedad que podría interesarte: \"{$propertyTitle}\"";
                } else {
                    $parts[] = 'Hemos encontrado una propiedad que podría interesarte';
                }

                if ($city) {
                    $parts[] = "en {$city}";
                }

                if ($price) {
                    $parts[] = "con un precio de $" . number_format($price, 2);
                }

                $description = implode(' ', $parts) . '.';
            }
        }

        // 3) Fallback súper seguro por si algo sigue vacío
        if (!$title) {
            $title = 'Notificación';
        }
        if (!$description) {
            $description = '';
        }

        // 4) Icono sugerido por tipo (se puede ampliar)
        $iconFromData = $data['icon'] ?? null;

        $icon = $iconFromData ?? match ($type) {
            ReservationPaidNotification::class      => 'fa-solid fa-receipt',
            NewAgentApplicationSubmitted::class     => 'fa-solid fa-user-plus',
            AgentApplicationApproved::class         => 'fa-solid fa-circle-check',
            NewMessageNotification::class           => 'fa-regular fa-comment-dots',
            NewPropertyMatchNotification::class     => 'fa-solid fa-house-circle-check',
            default                                 => 'fa-regular fa-bell',
        };

        return [
            'id'          => $notification->id,
            'icon'        => $icon,
            'title'       => $title,
            'description' => $description,
            'time'        => $notification->created_at
                                ? $notification->created_at->diffForHumans()
                                : '',
            'read'        => $notification->read_at !== null,
        ];
    });

    $view->with('unreadNotificationCount', $unreadNotificationCount)
         ->with('headerNotifications', $headerNotifications);
});

    }
}
