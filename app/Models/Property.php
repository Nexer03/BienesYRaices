<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PropertyReservation;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id', 'title', 'description', 'location', 'price', 'type','city',
    'bedrooms', // <-- ADD
    'bathrooms', // <-- ADD
    'status', 'latitude', 'longitude', 'listing_type'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function images()
    {
        return $this->hasMany(PropertyImage::class);
    }

    public function amenities()
    {
        return $this->belongsToMany(Amenity::class, 'amenity_property');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites');
    }


    public function reservations()
    {
        return $this->hasMany(PropertyReservation::class);
    }

    public function visits()
{
    return $this->hasMany(Visit::class);
}


}
