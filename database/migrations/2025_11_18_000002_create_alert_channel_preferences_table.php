<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_channel_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alert_criteria_id')->constrained('alert_criteria')->cascadeOnDelete();
            $table->string('channel');
            $table->string('frequency')->default('immediate');
            $table->boolean('enabled')->default(true);
            $table->timestamp('consented_at')->nullable();
            $table->timestamp('unsubscribed_at')->nullable();
            $table->timestamp('last_sent_at')->nullable();
            $table->unsignedInteger('cooldown_minutes')->default(30);
            $table->unsignedInteger('antispam_window_minutes')->default(60);
            $table->timestamps();

            $table->unique(['alert_criteria_id', 'channel']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_channel_preferences');
    }
};
