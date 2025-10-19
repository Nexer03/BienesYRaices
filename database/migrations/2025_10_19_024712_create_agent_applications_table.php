<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('agent_applications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            // Documentación
            $table->string('rfc', 13);
            $table->string('curp', 18);
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('agent_applications');
    }
};