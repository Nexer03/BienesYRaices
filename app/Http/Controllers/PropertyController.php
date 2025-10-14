<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\AmenityCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth; // Necesario para obtener el usuario logueado

class PropertyController extends Controller
{
    /**
     * Muestra una lista de todas las propiedades.
     */
   public function index(Request $request)
{
    // Empezamos la consulta
    $query = Property::query();

    // Si el request trae un filtro de ubicación, lo añadimos
    if ($request->has('location')) {
        $query->where('location', 'like', '%' . $request->location . '%');
    }

    // Si el request trae un filtro de precio máximo
    if ($request->has('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

    // Al final, ejecutamos la consulta y cargamos las relaciones
    $properties = $query->with('images', 'amenities')->latest()->get();

    return view('properties.index', ['properties' => $properties]);
}

    /**
     * Muestra el formulario para crear una nueva propiedad.
     */
    public function create()
    {
        // Obtiene las amenidades para mostrarlas en el formulario
        $amenityCategories = AmenityCategory::with('amenities')->get();
        return view('properties.create', ['amenityCategories' => $amenityCategories]);
    }

    /**
     * Guarda una nueva propiedad en la base de datos.
     */
    public function store(Request $request)
    {
        // 1. Validar los datos
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'price' => 'required|numeric',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'amenities' => 'array' // Valida que las amenidades sean un array
        ]);

        // 2. Crear la propiedad
        $property = Property::create([
            'user_id' => Auth::id(), // Asigna la propiedad al usuario logueado
            'title' => $validatedData['title'],
            'price' => $validatedData['price'],
            'location' => 'Ubicación de prueba', // Temporal
            'type' => 'apartment', // Temporal
            'status' => 'available' // Temporal
        ]);

        // 3. Guardar las imágenes
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $path = $imageFile->store('properties', 'public');
                $property->images()->create(['image_path' => $path]);
            }
        }

        // 4. Guardar las amenidades seleccionadas
        if(isset($validatedData['amenities'])){
            $property->amenities()->attach($validatedData['amenities']);
        }

        // 5. Redirigir con mensaje de éxito
        return redirect()->route('properties.create')->with('success', '¡Propiedad guardada con éxito!');
    }

    /**
     * Muestra el detalle de una propiedad específica.
     */
    public function show(Property $property)
    {
        // Carga las relaciones para mostrarlas en la vista de detalle
        $property->load('images', 'amenities.category', 'reviews.user', 'user');
        return view('properties.show', ['property' => $property]);
    }
}
