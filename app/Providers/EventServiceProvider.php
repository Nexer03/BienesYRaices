<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Mapping de eventos → listeners
     */
    protected $listen = [
        \App\Events\ReservationPaid::class => [
            \App\Listeners\SetPropertyRentedOnPaid::class,
            \App\Listeners\NotifyAdminsOfReservationPaid::class,
            \App\Listeners\NotifyGuestOfReservationPaid::class,
        ],

        \App\Events\PropertyPublishedOrUpdated::class => [
            \App\Listeners\DispatchAlertsForProperty::class,
        ],

        // Puedes agregar más eventos si lo necesitas
    ];

    /**
     * Registra eventos
     */
    public function boot(): void
    {
        //
    }

    /**
     * Indica si Laravel debe descubrir eventos automáticamente
     */
    public function shouldDiscoverEvents(): bool
    {
        return false; // conservamos el comportamiento estándar
    }
}
