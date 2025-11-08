<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::table('property_reservations', function (Blueprint $table) {
      if (!Schema::hasColumn('property_reservations','status')) {
        $table->string('status')->default('pending'); // pending|confirmed|cancelled...
      }
      if (!Schema::hasColumn('property_reservations','payment_status')) {
        $table->string('payment_status')->default('unpaid'); // unpaid|paid|refunded...
      }
    });
  }
  public function down(): void {
    Schema::table('property_reservations', function (Blueprint $table) {
      if (Schema::hasColumn('property_reservations','payment_status')) $table->dropColumn('payment_status');
      if (Schema::hasColumn('property_reservations','status'))         $table->dropColumn('status');
    });
  }
};
