<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Amenity extends Model
{
    use HasFactory;

    protected $fillable = ['amenity_category_id','name'];

    public function category()
    {
        return $this->belongsTo(AmenityCategory::class, 'amenity_category_id');
    }

    public function properties()
    {
        return $this->belongsToMany(Property::class, 'amenity_property');
    }
}
