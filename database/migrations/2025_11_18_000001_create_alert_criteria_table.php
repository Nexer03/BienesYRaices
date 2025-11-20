<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('alert_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->json('filters')->nullable();
            $table->string('frequency')->default('immediate');
            $table->boolean('is_paused')->default(false);
            $table->timestamp('consented_at')->nullable();
            $table->timestamp('last_sent_at')->nullable();
            $table->timestamp('last_matched_at')->nullable();
            $table->timestamp('last_consent_refresh_at')->nullable();
            $table->timestamp('last_unsubscribe_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('alert_criteria');
    }
};
