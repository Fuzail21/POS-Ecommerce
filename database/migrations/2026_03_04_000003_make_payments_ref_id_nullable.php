<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Step 1: make the column nullable first
        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('ref_id')->nullable()->change();
        });

        // Step 2: replace ref_id = 0 (sentinel for manual payments) with NULL
        DB::table('payments')->where('ref_type', 'manual')->where('ref_id', 0)
            ->update(['ref_id' => null]);
    }

    public function down(): void
    {
        // Restore NULL → 0 before making the column NOT NULL again
        DB::table('payments')->where('ref_type', 'manual')->whereNull('ref_id')
            ->update(['ref_id' => 0]);

        Schema::table('payments', function (Blueprint $table) {
            $table->unsignedBigInteger('ref_id')->nullable(false)->change();
        });
    }
};
