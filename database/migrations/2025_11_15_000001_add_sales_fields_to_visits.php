<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->decimal('sale_price', 12, 2)->nullable()->after('notes');
            $table->decimal('commission_percentage', 6, 3)->nullable()->after('sale_price');
            $table->decimal('commission_amount', 12, 2)->nullable()->after('commission_percentage');
            $table->timestamp('sale_recorded_at')->nullable()->after('commission_amount');
            $table->timestamp('commission_paid_at')->nullable()->after('sale_recorded_at');
        });
    }

    public function down(): void
    {
        Schema::table('visits', function (Blueprint $table) {
            $table->dropColumn([
                'sale_price',
                'commission_percentage',
                'commission_amount',
                'sale_recorded_at',
                'commission_paid_at',
            ]);
        });
    }
};
