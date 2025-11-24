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
        Schema::create('agent_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('agent_id')->constrained('users');
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('property_id')->constrained('properties');
            $table->foreignId('reservation_id')->nullable()->constrained('property_reservations')->nullOnDelete();
            $table->foreignId('visit_id')->nullable()->constrained('visits')->nullOnDelete();
            $table->enum('type', ['reservation', 'visit']);
            $table->text('message');
            $table->timestamps();

            $table->index(['agent_id', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agent_suggestions');
    }
};
