<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->get('type'); // 'sale' | 'rent' | null

        $query = Auth::user()
            ->favoriteProperties()
            ->with('images')
            ->latest('favorites.created_at');

        if (in_array($type, ['sale','rent'])) {
            $query->where('listing_type', $type);
        }

        $props = $query->paginate(12)->withQueryString();

        return view('favorites.index', [
            'props' => $props,
            'type'  => in_array($type, ['sale','rent']) ? $type : null,
        ]);
    }


    public function store(Property $property)
    {
        $user = Auth::user();
        $user->favoriteProperties()->syncWithoutDetaching([$property->id]);

        // fallback no-AJAX
        return back()->with('success', 'Propiedad guardada en favoritos.');
    }

    public function destroy(\App\Models\Property $property, \Illuminate\Http\Request $request)
    {
        // quita el favorito del usuario actual
        auth()->user()
            ->favoriteProperties()
            ->detach($property->id);

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Se quitó de tus favoritos.');
    }


    // AJAX toggle (devuelve JSON simple)
    public function toggle(Property $property)
    {
        $user = Auth::user();
        $exists = $user->favoriteProperties()->where('property_id', $property->id)->exists();

        if ($exists) {
            $user->favoriteProperties()->detach($property->id);
            return response()->json(['ok' => true, 'favorited' => false]);
        } else {
            $user->favoriteProperties()->attach($property->id);
            return response()->json(['ok' => true, 'favorited' => true]);
        }
    }
}
