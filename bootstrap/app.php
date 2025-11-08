<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // --- AÑADE TU ALIAS AQUÍ ---
        $middleware->alias([
            'admin' => \App\Http\Middleware\CheckAdminRole::class, // <-- Tu middleware
            'agent' => \App\Http\Middleware\CheckAgentRole::class,
        ]);

        // Aquí también puedes configurar grupos, middleware global, etc.
        // Ejemplo (puede que ya exista algo similar):
        // $middleware->web(append: [
        //     \App\Http\Middleware\ExampleMiddleware::class,
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ...
    })->create();
