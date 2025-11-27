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
    Schema::table('users', function (Blueprint $table) {
        // Ensure this line exists and is correct
        $table->timestamp('email_verified_at')->nullable()->after('email');

        // If 'remember_token' was already added in the 2025_10_11 migration,
        // you might need to REMOVE or comment out this line below to avoid errors:
        // $table->rememberToken();
    });
}

// Also check the down() method for consistency
public function down(): void
{
    Schema::table('users', function (Blueprint $table) {
        $table->dropColumn('email_verified_at');
        // $table->dropRememberToken(); // Only if you added rememberToken here
    });
}
};
