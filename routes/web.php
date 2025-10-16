<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;
use App\Models\Property;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

// Página principal
Route::get('/', function () {
    $properties = Property::with('images')->latest()->take(6)->get();
    return view('welcome', ['properties' => $properties]);
})->name('home');

// Detalle de una propiedad específica (pública para compartir enlaces)
Route::get('/properties/{property}', [PropertyController::class, 'show'])
    ->whereNumber('property') // Restringe a que {property} sea un número
    ->name('properties.show');

// Vista del mapa y su clave (públicas)
Route::get('/maps', function () {
    return view('maps');
})->name('maps');
Route::get('/maps-key', function () {
    return response()->json(['key' => config('services.google_maps.key')]);
})->name('maps.key');


/*
|--------------------------------------------------------------------------
| Rutas Protegidas (requieren autenticación)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Perfil del usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Grupo para la gestión de propiedades del usuario logueado
    Route::prefix('properties')->group(function () {
        Route::get('/', [PropertyController::class, 'index'])->name('properties.index');        // "Mis Propiedades"
        Route::get('/create', [PropertyController::class, 'create'])->name('properties.create');  // Formulario para crear
        Route::post('/', [PropertyController::class, 'store'])->name('properties.store');         // Guardar nueva propiedad
        Route::get('/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
        Route::put('/{property}', [PropertyController::class, 'update'])->name('properties.update');
        Route::delete('/property-images/{image}', [PropertyController::class, 'destroyImage'])->name('properties.images.destroy');
        Route::delete('/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
    });



});

require __DIR__.'/auth.php';
