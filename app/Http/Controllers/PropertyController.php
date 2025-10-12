<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Property;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    // Mostrar todas las propiedades en el mapa
public function index()
{
    $properties = Property::all();
    return view('properties.Properties', compact('properties')); 
}
 // Mostrar el formulario para crear una nueva propiedad
public function create()
{
    return view('properties.NewProperty'); 
}


    // Guardar nueva propiedad
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:house,apartment,land,office',
            'price' => 'required|numeric',
            'location' => 'required|string|max:255',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
        ]);

        Property::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'type' => $request->type,
            'price' => $request->price,
            'location' => $request->location,
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'status' => 'available'
        ]);

        return redirect()->route('properties.index')->with('success', 'Propiedad creada correctamente');
    }
}