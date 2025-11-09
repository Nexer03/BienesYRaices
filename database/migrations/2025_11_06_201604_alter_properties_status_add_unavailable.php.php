<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // En SQLite no existe MODIFY ni ENUM; saltamos el cambio para los tests.
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // MySQL / MariaDB: ampliar el ENUM para incluir 'unavailable'
        DB::statement("
            ALTER TABLE properties
            MODIFY COLUMN status ENUM('available','sold','rented','pending','unavailable')
            NOT NULL DEFAULT 'available'
        ");
    }

    public function down(): void
    {
        // En SQLite no hay nada que revertir porque no hicimos cambios
        if (DB::getDriverName() === 'sqlite') {
            return;
        }

        // MySQL / MariaDB: revertir (antes, normalizamos 'unavailable' a 'available')
        DB::statement("
            UPDATE properties
            SET status='available'
            WHERE status='unavailable';
        ");

        DB::statement("
            ALTER TABLE properties
            MODIFY COLUMN status ENUM('available','sold','rented','pending')
            NOT NULL DEFAULT 'available'
        ");
    }
};
