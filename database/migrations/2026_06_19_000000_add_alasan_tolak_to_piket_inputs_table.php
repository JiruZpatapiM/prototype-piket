<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('piket_inputs', function (Blueprint $table) {
            $table->text('alasan_tolak')->nullable();
        });

        // Update existing 'submitted' to 'approved'
        DB::table('piket_inputs')->where('status', 'submitted')->update(['status' => 'approved']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert 'approved' to 'submitted'
        DB::table('piket_inputs')->where('status', 'approved')->update(['status' => 'submitted']);

        Schema::table('piket_inputs', function (Blueprint $table) {
            $table->dropColumn('alasan_tolak');
        });
    }
};
