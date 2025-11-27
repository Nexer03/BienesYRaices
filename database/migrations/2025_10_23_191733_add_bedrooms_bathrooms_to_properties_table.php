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
    Schema::table('properties', function (Blueprint $table) {
        $table->tinyInteger('bedrooms')->unsigned()->nullable()->after('type'); // Add bedrooms after type
        $table->tinyInteger('bathrooms')->unsigned()->nullable()->after('bedrooms'); // Add bathrooms after bedrooms
    });
}

public function down(): void // To allow rollback
{
    Schema::table('properties', function (Blueprint $table) {
        $table->dropColumn(['bedrooms', 'bathrooms']);
    });
}
};
