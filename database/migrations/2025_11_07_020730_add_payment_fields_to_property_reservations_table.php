<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('property_reservations', function (Blueprint $table) {
            if (!Schema::hasColumn('property_reservations', 'payment_id')) {
                $table->string('payment_id', 100)->nullable()->after('status');
            }
            if (!Schema::hasColumn('property_reservations', 'payment_method')) {
                $table->string('payment_method', 30)->nullable()->after('payment_id');
            }
            // Solo si aún no lo tenías y te interesa guardarlo:
            if (!Schema::hasColumn('property_reservations', 'meta')) {
                $table->json('meta')->nullable()->after('payment_method');
            }
        });
    }

    public function down(): void
    {
        Schema::table('property_reservations', function (Blueprint $table) {
            if (Schema::hasColumn('property_reservations', 'meta')) {
                $table->dropColumn('meta');
            }
            if (Schema::hasColumn('property_reservations', 'payment_method')) {
                $table->dropColumn('payment_method');
            }
            if (Schema::hasColumn('property_reservations', 'payment_id')) {
                $table->dropColumn('payment_id');
            }
        });
    }
};
