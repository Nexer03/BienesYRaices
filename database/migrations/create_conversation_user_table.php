return new class extends Migration {
  public function up(): void {
    Schema::create('conversation_user', function (Blueprint $t) {
      $t->id();
      $t->foreignId('conversation_id')->constrained()->cascadeOnDelete();
      $t->foreignId('user_id')->constrained()->cascadeOnDelete();
      $t->enum('role', ['agent','client'])->nullable();
      $t->timestamps();
      $t->unique(['conversation_id','user_id']);
    });
  }
  public function down(): void { Schema::dropIfExists('conversation_user'); }
};
