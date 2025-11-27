<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('property_reservations', function (Blueprint $table) {

            // % aplicado al agente (dinámico según system_commissions)
            $table->decimal('commission_percentage', 5, 2)
                  ->nullable()
                  ->after('total_price');

            // Comisión total en dinero para el agente
            $table->decimal('agent_commission', 10, 2)
                  ->nullable()
                  ->after('commission_percentage');

            // Ganancia neta final del agente
            $table->decimal('agent_earnings', 10, 2)
                  ->nullable()
                  ->after('agent_commission');

            // Ganancia de la plataforma
            $table->decimal('platform_earnings', 10, 2)
                  ->nullable()
                  ->after('agent_earnings');
        });
    }

    public function down(): void
    {
        Schema::table('property_reservations', function (Blueprint $table) {
            $table->dropColumn([
                'commission_percentage',
                'agent_commission',
                'agent_earnings',
                'platform_earnings',
            ]);
        });
    }
};
