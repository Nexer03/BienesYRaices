<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        // Crea o actualiza el usuario admin por email
        User::updateOrCreate(
            ['email' => 'admin@admin.com'], // <- cambia si quieres otro correo
            [
                'name' => 'Admin',
                'password' => Hash::make('admin'), // contraseña: admin
                'phone' => null,
                'avatar' => null,
                'bio' => 'Administrador del sistema',
                'role' => 'admin',
                'email_verified_at' => now(),
            ]
        );
    }
}
