<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Aquí puedes registrar las rutas de la API de tu aplicación. Estas rutas
| son cargadas por el RouteServiceProvider y están asignadas al grupo
| de middleware "api".
|
*/

Route::get('/ping', function () {
    return response()->json(['status' => 'ok']);
});
