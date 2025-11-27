<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Property; // Importa el modelo Property
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            AdminUserSeeder::class,
        ]);

        // Crear algunos usuarios (incluyendo agentes) si no los tienes
       // User::factory(5)->create(['role' => 'client']);
        //User::factory(2)->create(['role' => 'agent']);

        // Llamar a los seeders de amenidades
        $this->call([
            AmenitySeeder::class,
            SaleAmenitySeeder::class,
        ]);

        // --- CREAR PROPIEDADES CON IMÁGENES ---
        // Llama a la PropertyFactory para crear 20 propiedades (cada una con sus imágenes)
        //Property::factory(20)->create();
    }
}
