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
        Schema::table('piket_inputs', function (Blueprint $table) {
            $table->string('status')->default('draft');
            $table->integer('score')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('piket_inputs', function (Blueprint $table) {
            $table->dropColumn(['status', 'score']);
        });
    }
};
