<?php

namespace App\Listeners;

use App\Events\PropertyPublishedOrUpdated;
use App\Services\AlertDispatchService;

class DispatchAlertsForProperty
{
    public function __construct(private readonly AlertDispatchService $dispatcher)
    {
    }

    public function handle(PropertyPublishedOrUpdated $event): void
    {
        $this->dispatcher->handlePropertyEvent($event->property, $event->trigger);
    }
}
