<?php

namespace App\Http\Controllers;

use App\Models\Property; // <-- AÑADIR ESTA LÍNEA
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // <-- AÑADIR ESTA LÍNEA

class FavoriteController extends Controller
{
    public function store(Property $property)
    {
        // Obtenemos al usuario que ha iniciado sesión
        $user = Auth::user();

        // Añadimos el registro a la tabla pivote 'favorites'
        $user->favorites()->attach($property->id);

        return back()->with('success', 'Propiedad añadida a favoritos.');
    }

    public function destroy(Property $property)
    {
        $user = Auth::user();

        // Quitamos el registro de la tabla pivote 'favorites'
        $user->favorites()->detach($property->id);

        return back()->with('success', 'Propiedad eliminada de favoritos.');
    }
}
