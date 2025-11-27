<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Agrega las columnas de INE frontal y reverso a la tabla agent_applications.
     */
    public function up(): void
    {
        Schema::table('agent_applications', function (Blueprint $table) {
            $table->string('ine_front')->nullable()->after('curp');
            $table->string('ine_back')->nullable()->after('ine_front');
        });
    }

    /**
     * Revierte los cambios si se ejecuta migrate:rollback.
     */
    public function down(): void
    {
        Schema::table('agent_applications', function (Blueprint $table) {
            $table->dropColumn(['ine_front', 'ine_back']);
        });
    }
};

