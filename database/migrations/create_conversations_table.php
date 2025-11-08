use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
  public function up(): void {
    Schema::create('conversations', function (Blueprint $t) {
      $t->id();
      $t->foreignId('property_id')->nullable()->constrained()->nullOnDelete();
      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('conversations'); }
};
