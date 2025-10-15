<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AmenityCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name','icon'];

    public function amenities()
    {
        return $this->hasMany(Amenity::class);
    }
}
