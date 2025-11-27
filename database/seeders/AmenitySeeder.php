<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AmenityCategory;

class AmenitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $amenities = [
            'Baño' => ['Secadora de pelo', 'Productos de limpieza', 'Shampoo', 'Acondicionador', 'Jabón corporal', 'Agua caliente', 'Gel de baño'],
            'Habitación y lavandería' => ['Lavadora', 'Secadora', 'Ganchos de ropa', 'Ropa de cama', 'Almohadas y mantas adicionales', 'Plancha', 'Espacio para guardar ropa'],
            'Entretenimiento' => ['Televisión', 'Netflix', 'Sistema de sonido', 'Libros y material de lectura'],
            'Para familias' => ['Cuna', 'Silla alta para bebés', 'Juegos de mesa', 'Libros y juguetes para niños'],
            'Calefacción y refrigeración' => ['Aire acondicionado', 'Calefacción', 'Ventilador de techo'],
            'Seguridad del hogar' => ['Detector de humo', 'Extintor de incendios', 'Botiquín de primeros auxilios', 'Detector de monóxido de carbono'],
            'Internet y oficina' => ['WiFi', 'Zona para trabajar'],
            'Cocina y comedor' => ['Cocina', 'Refrigerador', 'Microondas', 'Cafetera', 'Utensilios básicos de cocina', 'Platos y cubiertos', 'Congelador', 'Lavavajillas', 'Estufa', 'Horno', 'Mesa de comedor'],
            'Exteriores' => ['Patio o balcón', 'Parrilla para asados', 'Mobiliario exterior', 'Zona para fogatas'],
            'Estacionamiento e instalaciones' => ['Estacionamiento gratuito', 'Gimnasio', 'Alberca', 'Jacuzzi'],
        ];

        foreach ($amenities as $categoryName => $amenityList) {
            // Busca la categoría o la crea si no existe
            $category = AmenityCategory::firstOrCreate(['name' => $categoryName]);

            // Para cada amenidad en la lista, la busca o la crea si no existe
            foreach ($amenityList as $amenityName) {
                $category->amenities()->firstOrCreate(['name' => $amenityName]);
            }
        }
    }
}
