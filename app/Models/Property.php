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

    /** Estados permitidos (fuente de verdad) */
    public const STATUS_AVAILABLE   = 'available';
    public const STATUS_UNAVAILABLE = 'unavailable';
    public const STATUS_RENTED      = 'rented';
    public const STATUS_SOLD        = 'sold';

    /** Si mantendrás 'pending', descomenta: */
    // public const STATUS_PENDING     = 'pending';

    public const ALLOWED_STATUSES = [
        self::STATUS_AVAILABLE,
        self::STATUS_UNAVAILABLE,
        self::STATUS_RENTED,
        self::STATUS_SOLD,
        // self::STATUS_PENDING,
    ];

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
        'price' => 'float',
        'total_price' => 'float',
        'start_date'  => 'date',
        'end_date'    => 'date',

    ];

     /** Scopes */
     public function scopeStatus($q, string $status)
    {
        return $q->where('status', $status);
    }
    public function scopeAvailable($q)   { return $q->where('status', self::STATUS_AVAILABLE); }
    public function scopeUnavailable($q) { return $q->where('status', self::STATUS_UNAVAILABLE); }
    public function scopeRented($q)      { return $q->where('status', self::STATUS_RENTED); }
    public function scopeSold($q)        { return $q->where('status', self::STATUS_SOLD); }

    public function scopeByAgent($q, int $userId)
    {
        return $q->where('user_id', $userId);
    }

    public function scopeCity($q, string $city)
    {
        return $q->where('city', $city);
    }

    public function scopePriceBetween($q, float $min, float $max)
    {
        return $q->whereBetween('price', [$min, $max]);
    }

    /** Helpers de estado */
    public function isAvailable(): bool   { return $this->status === self::STATUS_AVAILABLE; }
    public function isUnavailable(): bool { return $this->status === self::STATUS_UNAVAILABLE; }
    public function isRented(): bool      { return $this->status === self::STATUS_RENTED; }
    public function isSold(): bool        { return $this->status === self::STATUS_SOLD; }



    /* ===================== Relaciones ===================== */

    public function user()        { return $this->belongsTo(User::class); }
    public function images()      { return $this->hasMany(PropertyImage::class); }
    public function amenities()   { return $this->belongsToMany(Amenity::class, 'amenity_property'); }
    public function reviews()     { return $this->hasMany(Review::class)->where('is_public', true)->latest(); }
    public function favoredBy()   { return $this->belongsToMany(User::class, 'favorites')->withTimestamps(); }
    public function reservations(){ return $this->hasMany(PropertyReservation::class); }
    public function visits()      { return $this->hasMany(Visit::class); }

    /** Etiqueta amigable para UI/exports */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            self::STATUS_AVAILABLE   => 'Disponible',
            self::STATUS_UNAVAILABLE => 'No disponible',
            self::STATUS_RENTED      => 'Rentada',
            self::STATUS_SOLD        => 'Vendida',
            default                  => ucfirst($this->status),
        };
    }

    /* ===================== Helpers de estado ===================== */

    /** ¿Está rentada en este momento (y la renta sigue vigente)? */
     public function isRentedNow(): bool
    {
        return $this->isRented() && $this->rented_until && now()->lt($this->rented_until);
    }

    public function isAvailableNow(): bool
    {
        if (!$this->isAvailable()) return false;
        if ($this->rented_until && now()->lt($this->rented_until)) return false;
        return true;
    }

    public function markUnavailable(): void
    {
        $this->update(['status' => self::STATUS_UNAVAILABLE]);
    }

    public function markAvailable(): void
    {
        $this->update(['status' => self::STATUS_AVAILABLE, 'rented_until' => null]);
    }

    public function markRentedUntil(\Carbon\Carbon|string $end): void
    {
        $endAt = $end instanceof \Carbon\Carbon ? $end : \Carbon\Carbon::parse($end);
        $this->update(['status' => self::STATUS_RENTED, 'rented_until' => $endAt]);
    }

    public function markSold(): void
    {
        $this->update(['status' => self::STATUS_SOLD, 'rented_until' => null]);
    }

    public function releaseIfExpired(): void
    {
        if ($this->isRented() && $this->rented_until && now()->gte($this->rented_until)) {
            $this->markAvailable();
        }
    }

}
