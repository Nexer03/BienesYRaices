<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('agent_applications', function (Blueprint $table) {
            $table->string('status')->default('pending')->after('curp');
            $table->text('rejection_reason')->nullable()->after('status');
        });
    }

    public function down()
    {
        Schema::table('agent_applications', function (Blueprint $table) {
            $table->dropColumn(['status', 'rejection_reason']);
        });
    }
};
