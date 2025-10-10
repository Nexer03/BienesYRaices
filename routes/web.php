<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});
Route::get('/welcome', function () {
    return view('welcome'); 
});
Route::get('/maps', function () {
    return view('maps');
});
Route::get('/maps-key', function () {
    return response()->json([
        'key' => config('services.google_maps.key')
    ]);
});