<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\User; // <-- Importar User model
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage; // Necesario para borrar imágenes

class AdminPropertyController extends Controller
{
    public function index(Request $request)
    {
        // Inicia la consulta para TODAS las propiedades
        $query = Property::query();

        // --- FILTROS ---
        // Filtrar por Agente (User ID)
        if ($request->filled('agent_id')) {
            $query->where('user_id', $request->input('agent_id'));
        }
        // TODO: Añadir otros filtros para admin si son necesarios (status, location, etc.)

        // Obtiene las propiedades con relaciones necesarias, paginadas
        $properties = $query->with(['user']) // Carga la relación con el usuario (agente)
                            ->latest()
                            ->paginate(20)
                            ->withQueryString(); // Mantiene los filtros en los enlaces de paginación

        // --- Obtener Agentes para el dropdown del filtro ---
        $agents = User::where('role', 'agent')->orderBy('name')->pluck('name', 'id'); // Obtiene pares 'nombre' => 'id'

        // Devuelve la vista de admin con los datos
        return view('admin.properties.index', [
            'properties' => $properties,
            'agents' => $agents, // Pasa los agentes a la vista
            'filters' => $request->only(['agent_id']) // Pasa los filtros actuales a la vista
        ]);
    }

    /**
     * Elimina la propiedad especificada (Acción de Admin).
     */
    public function destroy(Property $property) // Usa Route Model Binding
    {
        // Opcional: Doble verificación de rol (aunque el middleware ya lo hace)
        // if (Auth::user()->role !== 'admin') {
        //     abort(403, 'Acción no autorizada.');
        // }

        try {
            // 1. Borrar imágenes asociadas del almacenamiento
            foreach ($property->images as $image) {
                Storage::disk('public')->delete($image->image_path);
                // La tabla property_images se borrará en cascada si está configurado
            }

            // 2. Desasociar/Borrar relaciones (si no usan cascade en la BD)
            // ... (Las relaciones como amenities, reviews, etc., deberían borrarse en cascada si las migraciones tienen onDelete('cascade'))

            // 3. Eliminar la propiedad
            $property->delete(); // Esto dispara los onDelete('cascade') si están definidos

            return redirect()->route('admin.properties.index')
                         ->with('success', 'Propiedad eliminada exitosamente.');

        } catch (\Exception $e) {
            // Registrar el error para depuración
            \Log::error('Error al eliminar propiedad (Admin): ' . $e->getMessage());
            return redirect()->route('admin.properties.index')
                         ->with('error', 'Ocurrió un error al intentar eliminar la propiedad.');
        }
    }

} // Fin de la clase AdminPropertyController
