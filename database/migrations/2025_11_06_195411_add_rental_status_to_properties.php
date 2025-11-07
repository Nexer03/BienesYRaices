<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('properties', function (Blueprint $t) {
      // estados: available | unavailable | rented | sold
      $t->string('status')->default('available')->index()->change(); // si ya existe, ignora change
      $t->dateTime('rented_until')->nullable()->index();
    });
  }
  public function down(): void {
    Schema::table('properties', function (Blueprint $t) {
      $t->dropColumn('rented_until');
      // opcional: $t->string('status')->default('available')->change();
    });
  }
};
