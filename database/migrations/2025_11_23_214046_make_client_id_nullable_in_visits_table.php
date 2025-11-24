<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up()
{
    Schema::table('visits', function (Blueprint $table) {
        $table->unsignedBigInteger('client_id')->nullable()->change();
    });
}


    public function down(): void
    {
        // Volver a NOT NULL si quisieras revertir
        DB::statement('ALTER TABLE visits MODIFY client_id BIGINT UNSIGNED NOT NULL;');
    }
};
