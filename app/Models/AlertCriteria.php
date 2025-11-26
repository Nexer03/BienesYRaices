<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Arr;
use App\Models\Property;

class AlertCriteria extends Model
{
    use HasFactory;

    protected $table = 'alert_criteria';

    protected $fillable = [
        'user_id',
        'name',
        'filters',
        'frequency',
        'is_paused',
        'consented_at',
        'last_sent_at',
        'last_matched_at',
        'last_consent_refresh_at',
        'last_unsubscribe_at',
    ];

    protected $casts = [
        'filters'                 => 'array',
        'is_paused'               => 'boolean',
        'consented_at'            => 'datetime',
        'last_sent_at'            => 'datetime',
        'last_matched_at'         => 'datetime',
        'last_consent_refresh_at' => 'datetime',
        'last_unsubscribe_at'     => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function channelPreferences(): HasMany
    {
        return $this->hasMany(AlertChannelPreference::class);
    }

    public function deliveryLogs(): HasMany
    {
        return $this->hasMany(AlertDeliveryLog::class);
    }

    /**
     * Determina si esta propiedad hace match con los filtros del criterio.
     * Aquí solo delegamos a la lógica reutilizable.
     */
    public function matchesProperty(Property $property): bool
    {
        if ($this->is_paused) {
            return false;
        }

        $filters = $this->filters ?? [];

        return self::propertyMatchesFilters($property, $filters);
    }

    /**
     * Lógica central para decidir si una Property cumple con un arreglo de filtros.
     * Esta función la puedes reutilizar también desde otros lugares (por ejemplo,
     * para sacar propiedades recomendadas en el welcome).
     */
    public static function propertyMatchesFilters(Property $property, array $filters): bool
    {
        // 🔹 1) Status básico: no notificar vendidas / no disponibles
        if (in_array($property->status, ['sold', 'unavailable'], true)) {
            return false;
        }

        // 🔹 2) Ciudad (flexible – similar a cómo se filtra visualmente)
        $filterCity = trim((string) Arr::get($filters, 'city', ''));
        if ($filterCity !== '') {
            $propCity        = mb_strtolower(trim((string) $property->city));
            $filterCityLower = mb_strtolower($filterCity);

            // Si alguna viene vacía o ninguna contiene a la otra, no hace match
            if (
                $propCity === '' ||
                (!str_contains($propCity, $filterCityLower) && !str_contains($filterCityLower, $propCity))
            ) {
                return false;
            }
        }

        // 🔹 3) Tipo de publicación (rent/sale, etc.)
        $filterListingType = Arr::get($filters, 'listing_type');
        if (!empty($filterListingType) && (string) $property->listing_type !== (string) $filterListingType) {
            return false;
        }

        // 🔹 4) Tipo de propiedad (casa, departamento, etc.) si lo usas
        $filterType = Arr::get($filters, 'type');
        if (!empty($filterType) && (string) $property->type !== (string) $filterType) {
            return false;
        }

        // 🔹 5) Precio mínimo
        $priceMin = Arr::get($filters, 'price_min');
        if (!empty($priceMin) && (float) $property->price < (float) $priceMin) {
            return false;
        }

        // 🔹 6) Precio máximo
        $priceMax = Arr::get($filters, 'price_max');
        if (!empty($priceMax) && (float) $property->price > (float) $priceMax) {
            return false;
        }

        // 🔹 7) Recámaras mínimas
        $minBedrooms = Arr::get($filters, 'bedrooms');
        if (!empty($minBedrooms) && (int) $property->bedrooms < (int) $minBedrooms) {
            return false;
        }

        // 🔹 8) Baños mínimos
        $minBathrooms = Arr::get($filters, 'bathrooms');
        if (!empty($minBathrooms) && (int) $property->bathrooms < (int) $minBathrooms) {
            return false;
        }

        return true;
    }
}
