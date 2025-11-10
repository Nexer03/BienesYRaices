<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_commissions', function (Blueprint $table) {
            if (!Schema::hasColumn('system_commissions', 'customer_percentage')) {
                $table->decimal('customer_percentage', 5, 2)
                    ->default(0)
                    ->after('percentage');
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_commissions', function (Blueprint $table) {
            if (Schema::hasColumn('system_commissions', 'customer_percentage')) {
                $table->dropColumn('customer_percentage');
            }
        });
    }
};
