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
    // 1. Validar TODOS los nuevos datos
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|string',
            'price' => 'required|numeric',
            'location' => 'required|string',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'amenities' => 'nullable|array',
            'listing_type' => 'required|in:sale,rent',
        ]);

    // 2. Crear la propiedad con los nuevos campos
        $property = Property::create([
            'user_id' => Auth::id(),
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'type' => $validatedData['type'],
            'price' => $validatedData['price'],
            'location' => $validatedData['location'],
            'latitude' => $validatedData['latitude'],
            'longitude' => $validatedData['longitude'],
            'listing_type' => $validatedData['listing_type'], // <-- AÑADIR CAMPO AL CREAR
            'status' => 'available'
        ]);


        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('properties', 'public');
                $property->images()->create(['image_path' => $path]);
            }
        }

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
