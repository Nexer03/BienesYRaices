<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon; // <-- nuevo
use App\Models\PropertyReservation;
use App\Models\Review;

class Property extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'title', 'description', 'location', 'price', 'type', 'city',
        'bedrooms',            // <-- ya lo tenías
        'bathrooms',           // <-- ya lo tenías
        'status',              // available | unavailable | rented
        'latitude', 'longitude',
        'listing_type',        // sale | rent
        'rented_until',        // <-- NUEVO: asegúrate que exista en BD
    ];

    protected $casts = [
        'rented_until' => 'datetime', // <-- para trabajar como Carbon
    ];

    /* ===================== Relaciones ===================== */

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
        // Mostrar solo reseñas públicas y más recientes primero
        return $this->hasMany(Review::class)->where('is_public', true)->latest();
    }

    public function favoredBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function reservations()
    {
        return $this->hasMany(PropertyReservation::class);
    }

    public function visits()
    {
        return $this->hasMany(Visit::class);
    }

    /* ===================== Helpers de estado ===================== */

    /** ¿Está rentada en este momento (y la renta sigue vigente)? */
    public function isRentedNow(): bool
    {
        return $this->status === 'rented'
            && $this->rented_until
            && now()->lt($this->rented_until);
    }

    /** ¿Está disponible ahora (no rentada vigente y status available)? */
    public function isAvailableNow(): bool
    {
        // disponible solo si status=available y no hay renta vigente
        if ($this->status !== 'available') return false;
        if ($this->rented_until && now()->lt($this->rented_until)) return false;
        return true;
    }

    /** Marcar manualmente como NO disponible. */
    public function markUnavailable(): void
    {
        $this->update(['status' => 'unavailable']);
    }

    /** Marcar disponible (manual o al vencer renta). */
    public function markAvailable(): void
    {
        $this->update(['status' => 'available', 'rented_until' => null]);
    }

    /** Marcar como rentada hasta una fecha/hora final. */
    public function markRentedUntil(Carbon|string $end): void
    {
        $endAt = $end instanceof Carbon ? $end : Carbon::parse($end);
        $this->update(['status' => 'rented', 'rented_until' => $endAt]);
    }

    /** Si la renta ya venció, liberar automáticamente. */
    public function releaseIfExpired(): void
    {
        if ($this->status === 'rented' && $this->rented_until && now()->gte($this->rented_until)) {
            $this->markAvailable();
        }
    }


    public function markPending(): void
    {
        $this->update(['status' => 'pending']);
    }

    public function markSold(): void
    {
        $this->update(['status' => 'sold', 'rented_until' => null]);
    }

}
