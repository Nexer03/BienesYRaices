<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;
use App\Services\AlertDispatchService;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('alerts:send {frequency=immediate}', function (AlertDispatchService $dispatcher, string $frequency) {
    if (! in_array($frequency, ['immediate', 'daily', 'weekly'])) {
        $this->error('Frecuencia no soportada');
        return;
    }

    $dispatcher->dispatchScheduled($frequency);
    $this->info('Alertas enviadas con frecuencia ' . $frequency);
})->purpose('Envía alertas por frecuencia definida');

Schedule::command('alerts:send daily')->dailyAt('08:00');
Schedule::command('alerts:send weekly')->weeklyOn(1, '08:30');
