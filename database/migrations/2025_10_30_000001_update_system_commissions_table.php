<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('system_commissions', function (Blueprint $table) {
            if (!Schema::hasColumn('system_commissions', 'user_id')) {
                $table->foreignId('user_id')->nullable()->after('id')->constrained()->cascadeOnDelete();
            }

            if (!Schema::hasColumn('system_commissions', 'listing_type')) {
                $table->enum('listing_type', ['sale', 'rent', 'both'])->default('both')->after('user_id');
            }

            if (!Schema::hasColumn('system_commissions', 'effective_from')) {
                $table->date('effective_from')->nullable()->after('percentage');
            }

            if (!Schema::hasColumn('system_commissions', 'notes')) {
                $table->string('notes')->nullable()->after('effective_from');
            }
        });
    }

    public function down(): void
    {
        Schema::table('system_commissions', function (Blueprint $table) {
            if (Schema::hasColumn('system_commissions', 'notes')) {
                $table->dropColumn('notes');
            }

            if (Schema::hasColumn('system_commissions', 'effective_from')) {
                $table->dropColumn('effective_from');
            }

            if (Schema::hasColumn('system_commissions', 'listing_type')) {
                $table->dropColumn('listing_type');
            }

            if (Schema::hasColumn('system_commissions', 'user_id')) {
                $table->dropConstrainedForeignId('user_id');
            }
        });
    }
};
