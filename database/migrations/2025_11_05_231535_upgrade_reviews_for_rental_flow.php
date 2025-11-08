<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('reviews', function (Blueprint $t) {
            // Relacionar con reservas (para rentas tipo Airbnb)
            $t->foreignId('reservation_id')
              ->nullable()
              ->constrained('property_reservations')
              ->cascadeOnDelete();

            // Nuevo autor explícito (dejarás user_id como legacy por ahora)
            $t->foreignId('author_id')
              ->nullable()
              ->constrained('users')
              ->nullOnDelete();

            // Agente/anfitrión (dueño de la propiedad)
            $t->foreignId('agent_id')
              ->nullable()
              ->constrained('users')
              ->nullOnDelete();

            // Subratings (1–5) – todos opcionales para permitir migración suave
            $t->unsignedTinyInteger('cleanliness')->nullable();
            $t->unsignedTinyInteger('accuracy')->nullable();
            $t->unsignedTinyInteger('communication')->nullable();
            $t->unsignedTinyInteger('location')->nullable();
            $t->unsignedTinyInteger('value')->nullable();
            $t->unsignedTinyInteger('checkin')->nullable();

            // Promedio general (mantén tu rating actual, pero copia a este campo)
            $t->decimal('overall', 3, 2)->nullable();

            // Publicación/moderación
            $t->boolean('is_public')->default(true);
            $t->timestamp('published_at')->nullable();

            // Índices útiles
            $t->index(['property_id', 'overall']);
            $t->index(['reservation_id']);
        });

        // ---- Backfill: copiar datos legacy a los nuevos campos ----
        // author_id <- user_id
        DB::statement('UPDATE reviews SET author_id = user_id WHERE author_id IS NULL');

        // overall <- rating
        DB::statement('UPDATE reviews SET overall = rating WHERE overall IS NULL');

        // Opcional: marcar como publicados los existentes
        DB::statement('UPDATE reviews SET published_at = created_at WHERE published_at IS NULL');
    }

    public function down(): void
    {
        Schema::table('reviews', function (Blueprint $t) {
            // Al revertir, solo elimina lo nuevo (no toques user_id/rating legacy)
            $t->dropConstrainedForeignId('reservation_id');
            $t->dropConstrainedForeignId('author_id');
            $t->dropConstrainedForeignId('agent_id');

            $t->dropColumn([
                'cleanliness','accuracy','communication','location','value','checkin',
                'overall','is_public','published_at',
            ]);
        });
    }
};
