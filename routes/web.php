<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PropertyController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/maps-key', function () {
    return response()->json([
        'key' => env('GOOGLE_MAPS_API_KEY')
    ]);
});
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';


Route::middleware('auth')->group(function () {
     Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
     Route::get('/properties/new', [PropertyController::class, 'create'])->name('properties.create');
     Route::post('/properties/store', [PropertyController::class, 'store'])->name('properties.store');
     Route::get('/properties/map', [PropertyController::class, 'map'])->name('properties.map');
});

Route::middleware('auth')->group(function () {
    Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
});



Route::middleware('auth')->group(function () {
    Route::get('/properties', [PropertyController::class, 'index'])->name('properties.index');
    Route::get('/properties/new', [PropertyController::class, 'create'])->name('properties.create');
    Route::post('/properties/store', [PropertyController::class, 'store'])->name('properties.store');
    Route::get('/properties/map', [PropertyController::class, 'map'])->name('properties.map');
});
