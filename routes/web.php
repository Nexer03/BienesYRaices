<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;
use App\Models\Property;
use App\Models\UserPreference;
use App\Http\Controllers\AgentApplicationController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\UserPreferenceController;

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

// Página principal
Route::get('/', function () {
    // --- 1. Obtener Propiedades Recientes ---
    $properties = Property::with('images')->latest()->take(6)->get();

    // --- 2. Obtener Propiedades Recomendadas (si el usuario está logueado y tiene preferencias) ---
    $recommendedProperties = collect(); // Colección vacía por defecto
    $userPreferences = null;

    if (Auth::check()) {
        $userPreferences = Auth::user()->preferences; // Carga las preferencias (o null si no existen)

        if ($userPreferences) {
            // Inicia la consulta para recomendaciones (solo disponibles)
            $query = Property::with('images')->where('status', 'available');

            // Aplicar filtro de radio (si existe)
            if ($userPreferences->pref_latitude && $userPreferences->pref_longitude && $userPreferences->pref_radius) {
                $lat = $userPreferences->pref_latitude;
                $lng = $userPreferences->pref_longitude;
                $radius = $userPreferences->pref_radius / 1000; // Convertir a KM

                // Añade la fórmula Haversine y filtra por distancia
                $query->selectRaw("*, ( 6371 * acos( cos( radians(?) ) *
                                   cos( radians( latitude ) )
                                   * cos( radians( longitude ) - radians(?)
                                   ) + sin( radians(?) ) *
                                   sin( radians( latitude ) ) )
                                 ) AS distance", [$lat, $lng, $lat])
                      ->having("distance", "<", $radius)
                      ->orderBy("distance", 'asc');
            }
            // Si no hay radio, aplicar filtro de ubicación textual (si existe)
            elseif ($userPreferences->preferred_location) {
                 $query->where('location', 'like', '%' . $userPreferences->preferred_location . '%');
            }

            // Aplicar otros filtros de preferencias
            if ($userPreferences->min_price) {
                $query->where('price', '>=', $userPreferences->min_price);
            }
            if ($userPreferences->max_price) {
                $query->where('price', '<=', $userPreferences->max_price);
            }
            if ($userPreferences->preferred_listing_type) {
                $query->where('listing_type', $userPreferences->preferred_listing_type);
            }
            if ($userPreferences->min_bedrooms) {
                $query->where('bedrooms', '>=', $userPreferences->min_bedrooms);
            }
            if ($userPreferences->min_bathrooms) {
                $query->where('bathrooms', '>=', $userPreferences->min_bathrooms);
            }

            // Aplicar filtro de amenidades (si existen)
            if ($userPreferences->preferred_amenities) {
                $amenityIds = explode(',', $userPreferences->preferred_amenities);
                foreach ($amenityIds as $amenityId) {
                    if(trim($amenityId)){
                         $query->whereHas('amenities', function ($q) use ($amenityId) {
                            $q->where('amenities.id', trim($amenityId));
                         });
                    }
                }
            }

            // Si no se ordenó por distancia, ordenar por más reciente
            if (!($userPreferences->pref_latitude && $userPreferences->pref_longitude && $userPreferences->pref_radius)) {
                 $query->latest();
            }

            // Obtener hasta 6 propiedades recomendadas
            $recommendedProperties = $query->take(6)->get();
        }
    }
    //dd($recommendedProperties); // Detiene y muestra qué propiedades encontró la consulta
    // --- 3. Pasar Datos a la Vista ---
    return view('welcome', [
        'properties' => $properties, // Propiedades recientes
        'recommendedProperties' => $recommendedProperties, // Propiedades recomendadas
        'userPreferences' => $userPreferences // Preferencias (para el prompt)
    ]);
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
    // In routes/web.php, inside the 'auth' group

    // Rutas para las preferencias del usuario
    Route::get('/preferences/edit', [UserPreferenceController::class, 'edit'])->name('preferences.edit');
    Route::put('/preferences', [UserPreferenceController::class, 'update'])->name('preferences.update');

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
