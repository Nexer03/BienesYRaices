<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;
use App\Models\Property;


Route::get('/', function () {
    $properties = Property::with('images')->latest()->take(6)->get();
    return view('welcome', ['properties' => $properties]);
})->name('home');

Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');

Route::get('/maps', function () {
    return view('maps');
});
Route::get('/maps-key', function () {
    return response()->json([
        'key' => config('services.google_maps.key')
    ]);
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    Route::get('/properties/create', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/map', [PropertyController::class, 'map'])->name('properties.map');
});

require __DIR__.'/auth.php';

Route::get('/properties/{property}', [PropertyController::class, 'show'])->name('properties.show');
