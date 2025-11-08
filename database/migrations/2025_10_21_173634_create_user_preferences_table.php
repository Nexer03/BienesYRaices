// database/migrations/xxxx_xx_xx_xxxxxx_create_user_preferences_table.php
<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('user_preferences', function (Blueprint $table) {
            $table->foreignId('user_id')->primary()->constrained('users')->onDelete('cascade');
            $table->string('preferred_location')->nullable();
            $table->decimal('min_price', 11, 2)->nullable();
            $table->decimal('max_price', 11, 2)->nullable();
            $table->enum('preferred_listing_type', ['sale', 'rent'])->nullable();
            $table->tinyInteger('min_bedrooms')->unsigned()->nullable();
            $table->tinyInteger('min_bathrooms')->unsigned()->nullable();
            $table->text('preferred_amenities')->nullable();
            $table->timestamps();
        });
    }
    public function down(): void {
        Schema::dropIfExists('user_preferences');
    }
};
