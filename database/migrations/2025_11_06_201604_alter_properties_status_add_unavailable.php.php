<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        DB::statement("
          ALTER TABLE properties
          MODIFY COLUMN status ENUM('available','sold','rented','pending','unavailable')
          NOT NULL DEFAULT 'available'
        ");
    }

    public function down(): void
    {
        // si necesitas revertirlo (ojo: convertir 'unavailable' a 'available' antes)
        DB::statement("
          UPDATE properties SET status='available' WHERE status='unavailable';
        ");
        DB::statement("
          ALTER TABLE properties
          MODIFY COLUMN status ENUM('available','sold','rented','pending')
          NOT NULL DEFAULT 'available'
        ");
    }
};
