<?php

namespace App\Http\Controllers;

use App\Models\Property;
use App\Models\AmenityCategory;
use App\Models\PropertyImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Validation\Rule;
use App\Support\NotificationPresenter;
use App\Models\Visit;
use App\Models\Sale;
use App\Models\SystemCommission;
use App\Models\User;

class PropertyController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        $typeFilter = $request->get('type', 'rent');
        $userPreferences = null;
        $recommendedProperties = collect();
        $headerNotifications = collect();
        $unreadNotificationCount = 0;

        $rentMinRange = 100;
        $rentMaxRange = 10000;
        $saleMinRange = 500000;
        $saleMaxRange = 20000000;

        // --- 1. Filtros de búsqueda ---
        $query = Property::with('images')->whereNotNull('city');

        if ($request->filled('city')) {
            $query->where('city', $request->city);
        }

        if ($request->filled('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->filled('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        if (in_array($typeFilter, ['sale', 'rent'])) {
            $query->where('listing_type', $typeFilter);
        }

        // --- 2. Propiedades recientes (sin filtro) ---
        $properties = Property::with('images')
            ->latest()
            ->when(in_array($typeFilter, ['sale', 'rent']), function ($q) use ($typeFilter) {
                $q->where('listing_type', $typeFilter);
            })
            ->take(6)
            ->get();

        // --- 3. Agrupar propiedades por ciudad ---
        $filteredProperties = $query->latest()->get();
        $propertiesByCity = $filteredProperties->groupBy('city');

        // --- 4. Lista de ciudades únicas ---
        $cities = Property::select('city')
            ->distinct()
            ->whereNotNull('city')
            ->pluck('city')
            ->filter()
            ->values();

        // --- 5. Propiedades recomendadas según preferencias ---
        if (Auth::check()) {
            $userPreferences = Auth::user()->preferences;

            if ($userPreferences) {
                $prefQuery = Property::with('images')->where('status', 'available');
                $hasAmenityPreference = false;

                if ($userPreferences->pref_latitude && $userPreferences->pref_longitude && $userPreferences->pref_radius) {
                    $lat = $userPreferences->pref_latitude;
                    $lng = $userPreferences->pref_longitude;
                    $radius = $userPreferences->pref_radius / 1000; // km

                    $prefQuery->selectRaw(
                        "*, ( 6371 * acos( cos( radians(?) ) *
                                       cos( radians( latitude ) )
                                       * cos( radians( longitude ) - radians(?)
                                       ) + sin( radians(?) ) *
                                       sin( radians( latitude ) ) )
                                     ) AS distance",
                        [$lat, $lng, $lat]
                    )
                    ->having('distance', '<', $radius)
                    ->orderBy('distance', 'asc');
                } elseif ($userPreferences->preferred_location) {
                    $prefQuery->where('city', 'like', '%' . $userPreferences->preferred_location . '%');
                }

                if ($userPreferences->min_price) {
                    $prefQuery->where('price', '>=', $userPreferences->min_price);
                }
                if ($userPreferences->max_price) {
                    $prefQuery->where('price', '<=', $userPreferences->max_price);
                }
                if ($userPreferences->preferred_listing_type) {
                    $prefQuery->where('listing_type', $userPreferences->preferred_listing_type);
                }
                if ($userPreferences->min_bedrooms) {
                    $prefQuery->where('bedrooms', '>=', $userPreferences->min_bedrooms);
                }
                if ($userPreferences->min_bathrooms) {
                    $prefQuery->where('bathrooms', '>=', $userPreferences->min_bathrooms);
                }

                if ($userPreferences->preferred_amenities) {
                    $amenityIds = collect(explode(',', $userPreferences->preferred_amenities))
                        ->map(fn ($id) => (int) trim($id))
                        ->filter()
                        ->unique();

                    if ($amenityIds->isNotEmpty()) {
                        // Preferimos propiedades con amenities coincidentes, pero no excluimos
                        // aquellas que no tengan todas para no penalizar demasiado este filtro.
                        $prefQuery->withCount([
                            'amenities as matched_amenities_count' => fn ($q) => $q->whereIn('amenities.id', $amenityIds),
                        ]);
                        $hasAmenityPreference = true;
                    }
                }

                $requestedType = $request->input('type');
                if ($request->filled('type') && in_array($requestedType, ['sale', 'rent'])) {
                    $prefQuery->where('listing_type', $requestedType);
                }

                if (!($userPreferences->pref_latitude && $userPreferences->pref_longitude && $userPreferences->pref_radius)) {
                    $prefQuery->latest();
                }

                if ($hasAmenityPreference) {
                    // Priorizamos amenities solo como desempate: primero aplica orden por distancia/recencia.
                    $prefQuery->orderByDesc('matched_amenities_count');
                }

                $recommendedProperties = $prefQuery->take(6)->get();
            }
        }

        // --- 6. Notificaciones en header para cualquier usuario autenticado ---
        if (Auth::check()) {
            $user = Auth::user();
            $presenter = app(NotificationPresenter::class);

            $notifications = $user->notifications()
                ->latest()
                ->limit(5)
                ->get();

            $headerNotifications = $notifications->map(
                fn ($notification) => $presenter->summarize($notification, $user)
            );
            $unreadNotificationCount = $user->unreadNotifications()->count();
        }

        // IDs de propiedades favoritas del usuario autenticado
        $favoriteIds = [];
        if (Auth::check()) {
            $favoriteIds = Auth::user()
                ->favoriteProperties()
                ->pluck('id')
                ->toArray();
        }

        $noResults = $request->filled('city')
            || $request->filled('min_price')
            || $request->filled('max_price')
            || $request->filled('type');
        $noResults = $noResults && $filteredProperties->isEmpty();

        // --- 7. Enviar datos a la vista ---
        return view('welcome', [
            'properties'               => $properties,
            'recommendedProperties'    => $recommendedProperties,
            'userPreferences'          => $userPreferences,
            'typeFilter'               => $typeFilter,
            'propertiesByCity'         => $propertiesByCity,
            'cities'                   => $cities,
            'rentMinRange'             => $rentMinRange,
            'rentMaxRange'             => $rentMaxRange,
            'saleMinRange'             => $saleMinRange,
            'saleMaxRange'             => $saleMaxRange,
            'headerNotifications'      => $headerNotifications,
            'unreadNotificationCount'  => $unreadNotificationCount,
            'favoriteIds'              => $favoriteIds,
            'noResults'                => $noResults,
        ]);
    }

    public function myProperties(Request $request)
    {
        $query = Property::with(['images', 'amenities'])
            ->where('user_id', Auth::id())
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->status))
            ->when(
                $request->filled('city'),
                fn ($q) => $q->where('city', $request->city)
            )
            ->when(
                $request->filled('type') && in_array($request->type, ['rent', 'sale']),
                fn ($q) => $q->where('listing_type', $request->type)
            )
            ->latest('updated_at');

        $properties = $query->paginate(12)->withQueryString();

        // Para badge y <select> en UI
        $allowedStatuses = [
            Property::STATUS_AVAILABLE   => 'Disponible',
            Property::STATUS_UNAVAILABLE => 'No disponible',
            Property::STATUS_PENDING     => 'Pendiente',
            Property::STATUS_RENTED      => 'Rentada',
            Property::STATUS_SOLD        => 'Vendida',
        ];

        return view('properties.index', compact('properties', 'allowedStatuses'));
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
            'city'         => 'required|string|max:50',
            'price'        => 'required|numeric|min:0|max:99999999.99',
            'location'     => 'required|string|max:255',
            'latitude'     => ['required', 'numeric', 'between:-90,90'],
            'longitude'    => ['required', 'numeric', 'between:-180,180'],
            'listing_type' => 'required|in:sale,rent',
            'bedrooms'     => 'nullable|integer|min:1|max:20',
            'bathrooms'    => 'nullable|integer|min:1|max:20',
            'images.*'     => 'image|mimes:jpeg,png,jpg,gif|max:65536',
            'amenities'    => 'nullable|array',
        ]);

        $property = Property::create([
            'user_id'      => Auth::id(),
            'title'        => $validated['title'],
            'description'  => $validated['description'] ?? null,
            'type'         => $validated['type'],
            'city'         => $validated['city'],
            'price'        => $validated['price'],
            'location'     => $validated['location'],
            'latitude'     => $validated['latitude'],
            'longitude'    => $validated['longitude'],
            'listing_type' => $validated['listing_type'],
            'bedrooms'     => $validated['bedrooms'] ?? null,
            'bathrooms'    => $validated['bathrooms'] ?? null,
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

        // ⚠️ Ya NO llamamos al sistema viejo de notificación por preferencias.
        // El sistema nuevo de alertas se dispara vía eventos en el modelo Property
        // (PropertyPublishedOrUpdated + AlertDispatchService).

        return redirect()
            ->route('properties.my')
            ->with('success', 'Propiedad creada exitosamente.');
    }

    /**
     * Muestra los detalles de una propiedad.
     */
    public function show(Property $property)
    {
        $property->load('images', 'amenities.category', 'reviews.author', 'user');
        return view('properties.show', compact('property'));
    }

    /**
     * Muestra el formulario de edición de una propiedad.
     */
    public function edit(Property $property)
    {
        $this->authorize('update', $property);
        $amenityCategories = AmenityCategory::with('amenities')->get();
        return view('properties.EditProperty', compact('property', 'amenityCategories'));
    }

    /**
     * Actualiza una propiedad en la base de datos.
     */
    public function update(Request $request, Property $property)
    {
        $this->authorize('update', $property);

        $validated = $request->validate([
            'title'        => 'required|string|max:255',
            'description'  => 'nullable|string',
            'type'         => 'required|string',
            'city'         => 'required|string|max:50',
            'price'        => 'required|numeric|min:0|max:99999999.99',
            'location'     => 'required|string|max:255',
            'latitude'     => ['required', 'numeric', 'between:-90,90'],
            'longitude'    => ['required', 'numeric', 'between:-180,180'],
            'listing_type' => 'required|in:sale,rent',
            'bedrooms'     => 'nullable|integer|min:1|max:20',
            'bathrooms'    => 'nullable|integer|min:1|max:20',
            'images.*'     => 'image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'amenities'    => 'nullable|array',
        ]);

        // Evitar enviar arrays no fillable a update()
        $data = Arr::except($validated, ['images', 'amenities']);

        $property->update($data);

        // Añadir nuevas imágenes (si llegaron)
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('properties', 'public');
                $property->images()->create(['image_path' => $path]);
            }
        }

        // Sincronizar amenidades (si enviaste el campo)
        $property->amenities()->sync($validated['amenities'] ?? []);

        return redirect()
            ->route('properties.my')
            ->with('success', 'Propiedad actualizada correctamente.');
    }

    public function redirectToSaleCommission(Property $property)
    {
        $agent = auth()->user();

        // Buscar última visita COMPLETADA para esta propiedad y este agente
        $visit = Visit::where('property_id', $property->id)
            ->where('agent_id', $agent->id)
            ->where('status', 'completed')
            ->orderByDesc('visit_date')
            ->first();

        // Si no hay visita, la creamos automáticamente SIN cliente todavía
        if (!$visit) {
            $visit = Visit::create([
                'property_id' => $property->id,
                'agent_id'    => $agent->id,
                'client_id'   => null, // 👈 se asignará después al registrar la comisión
                'visit_date'  => now(),
                'status'      => 'completed',
                'notes'       => 'Visita generada automáticamente al marcar la propiedad como vendida para registrar la comisión.',
            ]);
        }

        // Redirigir al flujo normal de venta (formulario de comisión)
        return redirect()->route('agent.visits.sale', $visit);
    }




    /**
     * Elimina una propiedad y sus recursos.
     */
    public function destroy(Property $property)
    {
        $this->authorize('delete', $property);

        foreach ($property->images as $image) {
            Storage::disk('public')->delete($image->image_path);
        }

        $property->amenities()->detach();
        $property->delete();

        return redirect()
            ->route('properties.my')
            ->with('success', 'Propiedad eliminada correctamente.');
    }

    /**
     * Elimina una imagen específica de una propiedad.
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
     * Cambiar estado de disponibilidad de la propiedad.
     */
    public function toggleStatus(Request $request, Property $property)
    {
        $this->authorize('update', $property);

        // Bloquea cambios manuales si ya está vendida/rentada
        if ($property->isSold() || $property->isRented()) {
            return back()->with('error', 'No se puede cambiar estado manual cuando la propiedad está vendida o rentada.');
        }

        // Si mandas un destino explícito desde <select>, valídalo
        if ($request->filled('status')) {
            $request->validate([
                'status' => ['required', Rule::in(Property::ALLOWED_STATUSES)],
            ]);
            $property->update(['status' => $request->status]);
            return back()->with('success', "Estado actualizado a {$request->status}.");
        }

        // Mantener tu “toggle” original como fallback (compatible hacia atrás)
        // A) Si tienes 'unavailable' en DB:
        $next = $property->isAvailable() ? 'unavailable' : 'available';
        // B) Si NO tienes 'unavailable', usa 'pending' como “no disponible”:
        // $next = $property->isAvailable() ? 'pending' : 'available';

        $property->update(['status' => $next]);

        return back()->with('success', "Estado actualizado a {$next}.");
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
