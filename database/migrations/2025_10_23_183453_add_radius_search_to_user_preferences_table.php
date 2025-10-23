<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::table('user_preferences', function (Blueprint $table) {
        $table->decimal('pref_latitude', 10, 8)->nullable()->after('preferred_location');
        $table->decimal('pref_longitude', 11, 8)->nullable()->after('pref_latitude');
        $table->integer('pref_radius')->unsigned()->nullable()->after('pref_longitude'); // Radio en metros
    });
}

public function down(): void // Para poder revertir
{
    Schema::table('user_preferences', function (Blueprint $table) {
        $table->dropColumn(['pref_latitude', 'pref_longitude', 'pref_radius']);
    });
}
};
