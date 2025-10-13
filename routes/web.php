<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas (Cualquier visitante puede verlas)
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('home');
})->name('home');

// Rutas públicas para las propiedades
Route::get('/propiedades', [PropertyController::class, 'index'])->name('properties.index');

// Ruta para el mapa y su llave
Route::get('/maps', function () {
    return view('maps');
});
Route::get('/maps-key', function () {
    return response()->json(['key' => config('services.google_maps.key')]);
});

/*
|--------------------------------------------------------------------------
| Rutas Protegidas (Requieren que el usuario inicie sesión)
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    // La ruta específica 'crear' va aquí.
    Route::get('/propiedades/crear', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('/propiedades', [PropertyController::class, 'store'])->name('properties.store');

    // Aquí irían otras rutas que requieran login.
});

/*
|--------------------------------------------------------------------------
| Ruta Pública de Detalle (Va al final de las de propiedades)
|--------------------------------------------------------------------------
*/

// ¡CORREGIDO! La ruta de detalle con parámetro ahora está al final.
Route::get('/propiedades/{property}', [PropertyController::class, 'show'])->name('properties.show');


/*
|--------------------------------------------------------------------------
| Rutas de Autenticación (Breeze)
|--------------------------------------------------------------------------
*/

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Esta línea carga todas las rutas de login, registro, etc.
require __DIR__.'/auth.php';
