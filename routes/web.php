<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;
use App\Models\Property;
use App\Http\Controllers\AgentApplicationController;
use App\Http\Controllers\VisitController;

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
    ->whereNumber('property')
    ->name('properties.show');

Route::get('/maps-key', function () {
    return response()->json(['key' => config('services.google_maps.key')]);
})->name('maps.key');

Route::get('/properties-map', function () {
    $properties = Property::with('images')->get();
    return view('properties.Properties', ['properties' => $properties]);
})->name('properties.map');

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
        Route::get('/', [PropertyController::class, 'index'])->name('properties.index');
        Route::get('/create', [PropertyController::class, 'create'])->name('properties.create');
        Route::post('/', [PropertyController::class, 'store'])->name('properties.store');
        Route::get('/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
        Route::put('/{property}', [PropertyController::class, 'update'])->name('properties.update');
        Route::delete('/property-images/{image}', [PropertyController::class, 'destroyImage'])->name('properties.images.destroy');
        Route::delete('/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
    });

    // RUTAS DE VISITAS PARA TODOS LOS USUARIOS AUTENTICADOS
    Route::post('/visits', [VisitController::class, 'store'])->name('visits.store');
    Route::get('/my-visits', [VisitController::class, 'myVisits'])->name('visits.my');

    // RUTAS ESPECÍFICAS PARA AGENTES
    Route::middleware(['auth'])->group(function () {
        // Panel de agente
        Route::get('/agent-home', function () {
            if (auth()->user()->role === 'agent') {
                return view('agent.homeAgent');
            }
            return redirect()->route('agent.view')->with('error', 'No tienes acceso al panel de agentes');
        })->name('agent.home');

        // Visitas del agente
        Route::get('/agent/visits', [VisitController::class, 'agentVisits'])->name('agent.visits');

        // Acciones del agente sobre visitas
        Route::put('/visits/{visit}/confirm', [VisitController::class, 'confirm'])->name('visits.confirm');
        Route::put('/visits/{visit}/cancel', [VisitController::class, 'cancel'])->name('visits.cancel');
    });
});

// Formulario para convertirse en agente (acceso público)
Route::get('/agent-register', function () {
    // Si ya es agente, redirigir al panel
    if (auth()->check() && auth()->user()->role === 'agent') {
        return redirect()->route('agent.home');
    }
    return view('agent.newAgent');
})->name('agent.view');

// Procesar registro como agente
Route::post('/agent-register', [AgentApplicationController::class, 'store'])
    ->middleware('auth')
    ->name('agent.register.store');

require __DIR__.'/auth.php';