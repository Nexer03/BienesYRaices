<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\AmenityCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PropertyController extends Controller
{
    // Mostrar todas las propiedades
    public function index()
    {
        $properties = Property::with('images', 'amenities')->get();
        return view('properties.index', compact('properties'));
    }

    // Mostrar formulario para crear propiedad
    public function create()
    {
        // Trae las categorías de amenidades con sus amenidades
        $amenityCategories = AmenityCategory::with('amenities')->get();

        // Aquí usamos tu vista NewProperty.blade.php
        return view('properties.NewProperty', compact('amenityCategories'));
    }

    // Guardar propiedad
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:house,apartment,land,office',
            'price' => 'required|numeric',
            'location' => 'required|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'amenities' => 'array'
        ]);

        // Crear propiedad
        $property = Property::create([
            'user_id' => Auth::id(),
            'title' => $validatedData['title'],
            'description' => $validatedData['description'] ?? null,
            'type' => $validatedData['type'],
            'price' => $validatedData['price'],
            'location' => $validatedData['location'],
            'latitude' => $validatedData['latitude'],
            'longitude' => $validatedData['longitude'],
            'status' => 'available'
        ]);

        // Guardar imágenes
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('properties', 'public');
                $property->images()->create(['image_path' => $path]);
            }
        }

        // Guardar amenidades
        if (isset($validatedData['amenities'])) {
            $property->amenities()->attach($validatedData['amenities']);
        }

        return redirect()->route('properties.create')->with('success', '¡Propiedad guardada con éxito!');
    }

    // Mostrar detalle de propiedad
    public function show(Property $property)
    {
        $property->load('images', 'amenities.category', 'reviews.user', 'user');
        return view('properties.show', compact('property'));
    }
    public function map()
{
    $properties = Property::all(); // O con relaciones si quieres, ej: ->with('images','amenities')
    return view('properties.properties', compact('properties'));
}
}
