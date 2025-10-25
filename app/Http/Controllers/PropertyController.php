<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\AmenityCategory;
use App\Models\PropertyImage; // <-- Asegúrate de importar este modelo
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage; // <-- Y este para borrar archivos

class PropertyController extends Controller
{
    /**
     * Muestra la lista de propiedades del usuario autenticado.
     */
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Property::where('user_id', $userId);

        // --- LÓGICA DE FILTRADO AÑADIDA ---
        // Si se envía un filtro 'type' y es 'sale' o 'rent', lo aplicamos
        if ($request->filled('type') && in_array($request->type, ['sale', 'rent'])) {
            $query->where('listing_type', $request->type);
        }

        $properties = $query->with('images', 'amenities')->latest()->paginate(10);

        // Asegúrate de pasar la variable correcta (ya lo haces con compact)
        return view('properties.index', compact('properties'));
    }

    /**
     * Muestra el formulario para crear una nueva propiedad.
     */
    public function create()
    {
        $amenityCategories = AmenityCategory::with('amenities')->get();
        return view('properties.NewProperty', compact('amenityCategories'));
    }

    /**
     * Guarda una nueva propiedad en la base de datos.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'type'         => 'required|string',
            'price'        => 'required|numeric|min:0|max:99999999.99',
            'location'     => 'required|string|max:255',
            'latitude'     => 'required|numeric',
            'longitude'    => 'required|numeric',
            'listing_type' => 'required|in:sale,rent',
            'bedrooms'     => 'nullable|integer|min:1|max:20', // <-- ADD VALIDATION
            'bathrooms'    => 'nullable|integer|min:1|max:20', // <-- ADD VALIDATION
            'images.*'     => 'image|mimes:jpeg,png,jpg,gif|max:65536',
            'amenities'    => 'nullable|array',

        ]);

        $property = Property::create([
            'user_id'      => Auth::id(),
            'title'        => $validated['title'],
            'description'  => $validated['description'] ?? null,
            'type'         => $validated['type'],
            'price'        => $validated['price'],
            'location'     => $validated['location'],
            'latitude'     => $validated['latitude'],
            'longitude'    => $validated['longitude'],
            'listing_type' => $validated['listing_type'],
            'bedrooms'     => $validated['bedrooms'] ?? null, // <-- ADD FIELD
            'bathrooms'    => $validated['bathrooms'] ?? null, // <-- ADD FIELD
            'status'       => 'available',
        ]);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('properties', 'public');
                $property->images()->create(['image_path' => $path]);
            }
        }

        if (!empty($validated['amenities'])) {
            $property->amenities()->attach($validated['amenities']);
        }

        return redirect()->route('properties.index')->with('success', '¡Propiedad creada con éxito!');
    }

    /**
     * Muestra los detalles de una propiedad.
     */
    public function show(Property $property)
    {
        $property->load('images', 'amenities.category', 'reviews.user', 'user');
        return view('properties.show', compact('property'));
    }

    /**
     * Muestra el formulario de edición de una propiedad.
     */
    public function edit(Property $property)
    {
        if ($property->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para editar esta propiedad.');
        }

        $amenityCategories = AmenityCategory::with('amenities')->get();
        return view('properties.EditProperty', compact('property', 'amenityCategories'));
    }

    /**
     * Actualiza una propiedad en la base de datos.
     */
    public function update(Request $request, Property $property)
    {
        if ($property->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para modificar esta propiedad.');
        }

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'type'         => 'required|string',
            'price'        => 'required|numeric|min:0|max:99999999.99',
            'location'     => 'required|string|max:255',
            'latitude'     => 'required|numeric',
            'longitude'    => 'required|numeric',
            'listing_type' => 'required|in:sale,rent',
            'bedrooms'     => 'nullable|integer|min:1|max:20', // <-- ADD VALIDATION
            'bathrooms'    => 'nullable|integer|min:1|max:20', // <-- ADD VALIDATION
            'images.*'     => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'amenities'    => 'nullable|array',
        ]);

        $property->update($validated);

        // --- LÓGICA DE IMÁGENES MODIFICADA ---
        // Si el usuario sube nuevas imágenes, simplemente las añadimos a la galería existente.
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('properties', 'public');
                $property->images()->create(['image_path' => $path]);
            }
        }

        $property->amenities()->sync($validated['amenities'] ?? []);

        return redirect()->route('properties.index')->with('success', 'Propiedad actualizada correctamente.');
    }

    /**
     * Elimina una propiedad y sus recursos.
     */
    public function destroy(Property $property)
    {
        if ($property->user_id !== Auth::id()) {
            abort(403, 'No tienes permiso para eliminar esta propiedad.');
        }

        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $property->amenities()->detach();
        $property->delete();

        return redirect()->route('properties.index')->with('success', 'Propiedad eliminada exitosamente.');
    }

    /**
     * (NUEVO) Elimina una imagen específica de una propiedad.
     */
    public function destroyImage(PropertyImage $image)
    {
        if ($image->property->user_id !== Auth::id()) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }

        Storage::disk('public')->delete($image->image_path);
        $image->delete();

        return response()->json(['success' => true, 'message' => 'Imagen eliminada']);
    }

    /**
     * Muestra las propiedades en el mapa.
     */
    public function map()
    {
        $properties = Property::with('images', 'amenities')->get();
        return view('properties.properties', compact('properties'));
    }
}
