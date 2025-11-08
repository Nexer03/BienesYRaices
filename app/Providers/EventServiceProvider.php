<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * El arreglo $listen asocia eventos con sus listeners.
     */
    protected $listen = [
        \App\Events\ReservationPaid::class => [
            \App\Listeners\SetPropertyRentedOnPaid::class,
        ],
    ];

    /**
     * Registra cualquier evento para tu aplicación.
     */
    public function boot(): void
    {
        //
    }
}
