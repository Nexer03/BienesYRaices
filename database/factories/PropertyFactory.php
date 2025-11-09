<?php

namespace Database\Factories;

use App\Models\User;
use App\Models\Property as PropertyModel; // Usamos alias para evitar conflicto
use App\Models\Amenity; // <-- Importa el modelo Amenity
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\File; // Para la opción de imágenes locales
use Illuminate\Support\Facades\Storage; // Para la opción de imágenes locales

class PropertyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // ... (tu código definition() existente no cambia) ...

        $agent = User::where('role', 'agent')->inRandomOrder()->first();
        $agentId = $agent ? $agent->id : User::factory(['role' => 'agent'])->create()->id;

        return [
            'user_id' => $agentId,
            'title' => fake()->sentence(4) . ' ' . fake()->randomElement(['en Venta', 'en Renta']),
            'description' => fake()->paragraph(3),
            'type' => fake()->randomElement(['house', 'apartment', 'office']),
            'bedrooms' => fake()->numberBetween(1, 5),
            'bathrooms' => fake()->numberBetween(1, 4),
            'price' => fake()->numberBetween(500000, 20000000),
            'location' => fake()->address(),
            'city' => fake()->city(),
            'latitude' => fake()->latitude(20.6, 20.7),
            'longitude' => fake()->longitude(-105.3, -105.2),
            'listing_type' => fake()->randomElement(['sale', 'rent']),
            'status' => 'available',
        ];
    }

    /**
     * Configura el estado del modelo después de la creación.
     */
    public function configure(): static
    {
        return $this->afterCreating(function (PropertyModel $property) {

            // --- Lógica para Imágenes (existente) ---
            $numberOfImages = rand(3, 7);
            for ($i = 0; $i < $numberOfImages; $i++) {
                // Elige aquí tu método preferido para generar imágenes
                $imagePath = 'placeholders/property_placeholder.jpg'; // Ejemplo con placeholder
                $property->images()->create(['image_path' => $imagePath]);
            }

            // --- NUEVO: Lógica para Amenidades ---
            // 1. Obtenemos todos los IDs de las amenidades disponibles
            $amenityIds = Amenity::pluck('id')->toArray();

            // 2. Si hay amenidades, seleccionamos un número aleatorio (ej: entre 5 y 15)
            if (!empty($amenityIds)) {
                $numberOfAmenities = rand(5, min(15, count($amenityIds))); // No más de las que existen

                // 3. Barajamos los IDs y tomamos la cantidad aleatoria
                shuffle($amenityIds);
                $amenitiesToAttach = array_slice($amenityIds, 0, $numberOfAmenities);

                // 4. Adjuntamos las amenidades seleccionadas a la propiedad
                $property->amenities()->attach($amenitiesToAttach);
            }
        });
    }
}
