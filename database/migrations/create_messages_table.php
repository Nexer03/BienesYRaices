return new class extends Migration {
  public function up(): void {
    Schema::create('messages', function (Blueprint $t) {
      $t->id();
      $t->foreignId('conversation_id')->constrained()->cascadeOnDelete();
      $t->foreignId('user_id')->constrained()->cascadeOnDelete();
      $t->text('body');
      $t->timestamp('read_at')->nullable();
      $t->timestamps();
    });
  }
  public function down(): void { Schema::dropIfExists('messages'); }
};
