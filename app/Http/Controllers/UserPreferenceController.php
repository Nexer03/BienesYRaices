<?php

namespace App\Http\Controllers;

use App\Models\UserPreference;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AmenityCategory;

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
        $userId = Auth::id();

        $validated = $request->validate([
            'preferred_location'     => 'nullable|string|max:255',
            'pref_latitude'          => 'nullable|numeric|between:-90,90',       // <-- Verifica esta regla
            'pref_longitude'         => 'nullable|numeric|between:-180,180',   // <-- Verifica esta regla
            'pref_radius'            => 'nullable|integer|min:100',          // <-- Verifica esta regla
            'min_price'              => 'nullable|numeric|min:0',
            'max_price'              => 'nullable|numeric|min:0|gte:min_price',
            'preferred_listing_type' => 'nullable|in:sale,rent',
            'min_bedrooms'           => 'nullable|integer|min:0',
            'min_bathrooms'          => 'nullable|integer|min:0',
            'preferred_amenities'    => 'nullable|string',
        ]);

        // Use updateOrCreate: Finds by user_id or creates if not found, then updates with validated data
        UserPreference::updateOrCreate(
            ['user_id' => $userId], // Search criteria
            $validated             // Data to update or insert
        );

        // Redirect back to the dashboard or home page with a success message
        return redirect()->route('home')->with('status', '¡Preferencias guardadas con éxito!');
    }
}
