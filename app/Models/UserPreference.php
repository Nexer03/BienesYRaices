<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserPreference extends Model
{
    use HasFactory;

    // Indicates that the primary key is 'user_id' and it's not auto-incrementing
    protected $primaryKey = 'user_id';
    public $incrementing = false;

    // Define which columns can be mass-assigned
    protected $fillable = [
        'user_id',
        'preferred_location', // Puedes mantenerlo o quitarlo
        'pref_latitude',      // <-- Añadir
        'pref_longitude',     // <-- Añadir
        'pref_radius',        // <-- Añadir
        'min_price',
        'max_price',
        'preferred_listing_type',
        'min_bedrooms',
        'min_bathrooms',
        'preferred_amenities',
    ];

    /**
     * Get the user that owns the preferences.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
