<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('property_reservations', function (Blueprint $t) {
            if (!Schema::hasColumn('property_reservations', 'status')) {
                $t->string('status', 20)->default('pending')->index();
            }
            if (!Schema::hasColumn('property_reservations', 'total_price')) {
                $t->decimal('total_price', 10, 2)->nullable();
            }
            if (!Schema::hasColumn('property_reservations', 'paid_at')) {
                $t->timestamp('paid_at')->nullable();
            }
        });
    }
    public function down(): void {
        Schema::table('property_reservations', function (Blueprint $t) {
            // opcional: $t->dropColumn([...]);
        });
    }
};
