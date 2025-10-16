<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AmenityCategory;

class SaleAmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenitiesForSale = [
            'Cocina y Electrodomésticos' => [
                'Cocina integral',
                'Barra de desayuno',
                'Alacena',
                'Campana extractora',
                'Triturador de basura',
                'Conexión para lavavajillas',
            ],
            'Exterior y Lote' => [
                'Cochera techada',
                'Jardín',
                'Patio trasero',
                'Terraza',
                'Balcón',
                'Cisterna',
                'Sistema de riego',
            ],
            'Características Interiores' => [
                'Sala de estar',
                'Comedor',
                'Estudio / Oficina',
                'Cuarto de servicio',
                'Bodega',
                'Closets',
                'Walk-in closet',
            ],
            'Servicios y Seguridad' => [
                'Portón eléctrico',
                'Circuito cerrado (CCTV)',
                'Seguridad 24 horas',
                'Área de lavado',
                'Conexión de gas natural',
                'Calentador de agua (Boiler)',
            ],
        ];

        foreach ($amenitiesForSale as $categoryName => $amenityList) {
            // Find the category or create it if it doesn't exist
            $category = AmenityCategory::firstOrCreate(['name' => $categoryName]);

            // Create the amenities for that category
            foreach ($amenityList as $amenityName) {
                $category->amenities()->firstOrCreate(['name' => $amenityName]);
            }
        }
    }
}
