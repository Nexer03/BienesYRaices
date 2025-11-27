<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('conversations', function (Blueprint $table) {
      $table->id();
      $table->foreignId('property_id')->constrained()->cascadeOnDelete();
      $table->foreignId('agent_id')->constrained('users')->cascadeOnDelete();
      $table->foreignId('client_id')->constrained('users')->cascadeOnDelete();
      $table->timestamps();
      $table->unique(['property_id', 'agent_id', 'client_id']); // evita duplicados
    });
  }

  public function down(): void {
    Schema::dropIfExists('conversations');
  }
};

