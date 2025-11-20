<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_delivery_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alert_criteria_id')->constrained('alert_criteria')->cascadeOnDelete();
            $table->string('channel');
            $table->string('status')->default('delivered');
            $table->timestamp('sent_at');
            $table->unsignedInteger('properties_count')->default(0);
            $table->string('frequency')->default('immediate');
            $table->string('dedup_hash')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['channel', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_delivery_logs');
    }
};
