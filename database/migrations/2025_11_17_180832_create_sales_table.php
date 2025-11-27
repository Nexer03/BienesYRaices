<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::create('sales', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('property_id');
        $table->unsignedBigInteger('agent_id');
        $table->unsignedBigInteger('client_id');
        $table->unsignedBigInteger('visit_id');

        $table->decimal('sale_price', 12, 2);
        $table->decimal('commission_percentage', 5, 2);
        $table->decimal('commission_amount', 12, 2);

        $table->text('notes')->nullable();

        $table->timestamps();

        $table->foreign('property_id')->references('id')->on('properties')->onDelete('cascade');
        $table->foreign('agent_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('client_id')->references('id')->on('users')->onDelete('cascade');
        $table->foreign('visit_id')->references('id')->on('visits')->onDelete('cascade');
    });
}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};
