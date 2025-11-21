<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AmenityCategory;
use App\Models\AlertCriteria;


class UserPreferenceController extends Controller
{
    /**
     * Show the form for editing the user's preferences.
     * If preferences don't exist, it shows an empty form.
     */
    public function edit()
    {
        // Get the current user's ID
        $userId = Auth::id();

        // Find the preferences for this user, or create a new empty instance
        // This ensures $preferences is always an object, simplifying the view
        $preferences = UserPreference::firstOrNew(['user_id' => $userId]);
        $amenityCategories = AmenityCategory::with('amenities')->get();

        return view('preferences.edit', compact('preferences', 'amenityCategories'));
    }

    /**
     * Update the user's preferences in the database.
     * Uses updateOrCreate to handle both creating and updating.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        $userId = $user->id;

        $validated = $request->validate([
            'preferred_location'      => 'nullable|string|max:255',
            'min_price'               => 'nullable|numeric|min:0',
            'max_price'               => 'nullable|numeric|min:0|gte:min_price',
            'preferred_listing_type'  => 'nullable|in:sale,rent',
            'min_bedrooms'            => 'nullable|integer|min:0',
            'min_bathrooms'           => 'nullable|integer|min:0',
            'preferred_amenities'     => 'nullable|string',
        ]);

        // 1) Guardar/actualizar preferencias del usuario
        UserPreference::updateOrCreate(
            ['user_id' => $userId],
            $validated
        );

        // 2) Mapear preferencias -> filtros del criterio de alerta
        $filters = [
            'city'         => $validated['preferred_location']      ?? null,
            'listing_type' => $validated['preferred_listing_type']  ?? null,
            'price_min'    => $validated['min_price']               ?? null,
            'price_max'    => $validated['max_price']               ?? null,
            'bedrooms'     => $validated['min_bedrooms']            ?? null,
            'bathrooms'    => $validated['min_bathrooms']           ?? null,
        ];

        // Quitar nulos/vacíos
        $filters = array_filter($filters, fn ($v) => !is_null($v) && $v !== '');

        // Si el usuario puso al menos algún criterio, creamos/actualizamos la alerta
        if (!empty($filters)) {
            $criteria = $user->alertCriteria()->updateOrCreate(
                ['name' => 'Mis preferencias'],
                [
                    'filters'                 => $filters,
                    'frequency'               => 'immediate',   // queremos alertas al instante
                    'is_paused'               => false,
                    'consented_at'            => now(),
                    'last_consent_refresh_at' => now(),
                ]
            );

            // Canal "campanita" (bell) con frecuencia inmediata
            $criteria->channelPreferences()->updateOrCreate(
                ['channel' => 'bell'],
                [
                    'frequency'               => 'immediate',
                    'enabled'                 => true,
                    'consented_at'            => now(),
                    'cooldown_minutes'        => 0,
                    'antispam_window_minutes' => 60,
                ]
            );
        }

        return redirect()
            ->route('home')
            ->with('status', '¡Preferencias y alertas guardadas con éxito!');
    }

}
