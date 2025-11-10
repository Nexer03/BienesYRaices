<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PropertyController;
use Illuminate\Support\Facades\Route;
use App\Models\Property;
use App\Http\Controllers\AgentApplicationController;
use App\Http\Controllers\Admin\AgentApplicationController as AdminAgentApplicationController;
use App\Http\Controllers\VisitController;
use App\Http\Controllers\UserPreferenceController;
use App\Http\Controllers\AdminPropertyController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminReportController;
use App\Http\Controllers\PropertyReservationController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\FavoriteController;
use App\Http\Controllers\ReservationController;
use App\Http\Controllers\PayPalController;
use App\Http\Controllers\AgentReservationController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ChatController;


Route::middleware('auth')->group(function () {
    // Inbox de chats (lista de conversaciones del usuario logueado)
    Route::get('/chats', [ChatController::class, 'index'])->name('chat.index');

    // Abrir chat por propiedad (cliente inicia)
    Route::get('/chat/{property}', [ChatController::class, 'show'])->name('chat.show');

    // Abrir chat por conversación (agente entra desde inbox)
    Route::get('/chat/c/{conversation}', [ChatController::class, 'open'])->name('chat.open');

    // Enviar mensaje
    Route::post('/chat/{conversation}/send', [ChatController::class, 'send'])->name('chat.send');
});

/*
|--------------------------------------------------------------------------
| Rutas Públicas
|--------------------------------------------------------------------------
*/

// Página principal (listado general de propiedades)
Route::get('/', [PropertyController::class, 'index'])->name('home');

// Detalle de una propiedad específica
Route::get('/properties/{property}', [PropertyController::class, 'show'])
    ->whereNumber('property')
    ->name('properties.show');

// Clave de Google Maps
Route::get('/maps-key', function () {
    return response()->json(['key' => config('services.google_maps.key')]);
})->name('maps.key');

// Vista del mapa con todas las propiedades
Route::get('/properties-map', function () {
    $properties = Property::with('images')->get();
    return view('properties.Properties', ['properties' => $properties]);
})->name('properties.map');

// Formulario para convertirse en agente
Route::get('/agent-register', function () {
    if (auth()->check() && auth()->user()->role === 'agent') {
        return redirect()->route('agent.home');
    }
    return view('agent.newAgent');
})->name('agent.view');

// Procesar registro como agente
Route::post('/agent-register', [AgentApplicationController::class, 'store'])
    ->middleware('auth')
    ->name('agent.register.store');

// Procesar reservas
Route::post('/reservations', [PropertyReservationController::class, 'store']);



/*
|--------------------------------------------------------------------------
| Rutas Protegidas (requieren autenticación)
|--------------------------------------------------------------------------
*/
// routes/web.php
Route::middleware(['auth'])->group(function () {
    Route::patch('/reservations/{reservation}/confirm-payment', [\App\Http\Controllers\PropertyReservationController::class, 'confirmPayment'])
        ->name('reservations.confirmPayment');
});

Route::middleware(['auth'])->group(function () {

    // Crear reserva “pendiente de pago” a partir de fechas seleccionadas
    Route::post('/reservations/preview', [ReservationController::class, 'preview'])
        ->name('reservations.preview');

    // Pantalla de checkout estilo Airbnb
    Route::get('/reservations/{reservation}/checkout', [ReservationController::class, 'checkout'])
        ->name('reservations.checkout');

    // PayPal
    Route::post('/paypal/create-order', [\App\Http\Controllers\PayPalController::class, 'createOrder'])
        ->name('paypal.createOrder');
    Route::post('/paypal/capture-order', [\App\Http\Controllers\PayPalController::class, 'captureOrder'])
        ->name('paypal.captureOrder');

    // NUEVAS: para cuando PayPal redirige por GET con ?token=
    Route::get('/paypal/capture', [PayPalController::class, 'captureReturn'])->name('paypal.captureReturn');
    Route::get('/paypal/cancel',  [PayPalController::class, 'cancelReturn'])->name('paypal.cancelReturn');

});


Route::middleware(['auth', 'verified'])->group(function () {

    // Dashboard
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    Route::post('/properties/{property}/reviews', [ReviewController::class, 'store'])
        ->name('reviews.store');

    // Perfil del usuario
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Preferencias del usuario
    Route::get('/preferences/edit', [UserPreferenceController::class, 'edit'])->name('preferences.edit');
    Route::put('/preferences', [UserPreferenceController::class, 'update'])->name('preferences.update');

    // Favoritos del usuario logeado
    Route::get('/favorites', [FavoriteController::class, 'index'])->name('favorites.index');
    Route::post('/favorites/{property}', [FavoriteController::class, 'store'])->name('favorites.store');
    Route::delete('/favorites/{property}', [FavoriteController::class, 'destroy'])
    ->middleware('auth')
    ->name('favorites.destroy');



    // AJAX toggle opcional (devuelve JSON)
    Route::post('/favorites/{property}/toggle', [FavoriteController::class, 'toggle'])->name('favorites.toggle');


    // Visitas
    Route::get('/my-visits', [VisitController::class, 'myVisits'])->name('visits.my');

    // Panel de agentes y rutas específicas
    Route::middleware(['auth'])->group(function () {
        Route::get('/agent-home', function () {
            if (auth()->user()->role === 'agent') {
                return view('agent.homeAgent');
            }
            return redirect()->route('agent.view')->with('error', 'No tienes acceso al panel de agentes');
        })->name('agent.home');

    });
});
/*
|--------------------------------------------------------------------------
| Rutas de Agente
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'agent'])->group(function () {
    Route::prefix('properties')->group(function () {
        Route::get('/', [PropertyController::class, 'index'])->name('properties.index'); // listado general
        Route::get('/my', [PropertyController::class, 'myProperties'])->name('properties.my'); // listado del usuario/ agente
        Route::get('/create', [PropertyController::class, 'create'])->name('properties.create');
        Route::post('/', [PropertyController::class, 'store'])->name('properties.store');
        Route::get('/{property}/edit', [PropertyController::class, 'edit'])->name('properties.edit');
        Route::put('/{property}', [PropertyController::class, 'update'])->name('properties.update');
        Route::delete('/property-images/{image}', [PropertyController::class, 'destroyImage'])->name('properties.images.destroy');
        Route::delete('/{property}', [PropertyController::class, 'destroy'])->name('properties.destroy');
        Route::patch('/{property}/status', [PropertyController::class, 'toggleStatus'])
        ->name('properties.toggleStatus');
        Route::get('agent/visits/feed', [\App\Http\Controllers\VisitController::class, 'feed'])

    ->name('agent.visits.feed');

    });

    // ---- Visits del agente (nuevo) ----
    Route::prefix('agent')->name('agent.')->group(function () {
        // CRUD de visitas para el agente
        Route::get('visits', [VisitController::class, 'index'])->name('visits.index');
        Route::get('visits/create', [VisitController::class, 'create'])->name('visits.create');
        Route::post('visits', [VisitController::class, 'store'])->name('visits.store');
        Route::get('visits/{visit}/edit', [VisitController::class, 'edit'])->name('visits.edit');
        Route::put('visits/{visit}', [VisitController::class, 'update'])->name('visits.update');
        Route::delete('visits/{visit}', [VisitController::class, 'destroy'])->name('visits.destroy');
        // Rutas del calendario (feed JSON)
        Route::get('agent/visits/feed', [VisitController::class, 'feed'])->name('agent.visits.feed');

       
        Route::patch('visits/{visit}/status', [VisitController::class, 'updateStatus'])->name('visits.status');
    });
    Route::get('/agent/analytics', [\App\Http\Controllers\AgentAnalyticsController::class, 'index'])
    ->name('agent.analytics');
   Route::prefix('agent')->name('agent.')->group(function () {
    Route::get('reservations', [AgentReservationController::class, 'index'])
        ->name('reservations.index');
    Route::get('reservations/feed', [AgentReservationController::class, 'feed'])
        ->name('reservations.feed');
    });
});
/*
|--------------------------------------------------------------------------
| Rutas de Administrador
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'verified', 'admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

    Route::get('/properties', [AdminPropertyController::class, 'index'])->name('properties.index');
    Route::delete('/properties/{property}', [AdminPropertyController::class, 'destroy'])->name('properties.destroy');

    Route::resource('users', AdminUserController::class);

    Route::get('/reports/sales', [AdminReportController::class, 'salesReport'])->name('reports.sales');
    Route::get('/reports/visits', [AdminReportController::class, 'visitsReport'])->name('reports.visits');
    Route::get('/reports/properties/export', [AdminReportController::class, 'exportPropertyReport'])->name('reports.properties.export');

    Route::get('agent-applications', [AdminAgentApplicationController::class, 'index'])->name('agent-applications.index');
    Route::get('agent-applications/{agentApplication}', [AdminAgentApplicationController::class, 'show'])->name('agent-applications.show');
    Route::post('agent-applications/{agentApplication}/approve', [AdminAgentApplicationController::class, 'approve'])->name('agent-applications.approve');
    Route::post('agent-applications/{agentApplication}/reject', [AdminAgentApplicationController::class, 'reject'])->name('agent-applications.reject');
});

/*
|--------------------------------------------------------------------------
| Auth routes
|--------------------------------------------------------------------------
*/
require __DIR__.'/auth.php';
