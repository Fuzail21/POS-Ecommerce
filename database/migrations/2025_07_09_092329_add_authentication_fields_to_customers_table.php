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
        Schema::table('customers', function (Blueprint $table) {
            $table->string('password')->after('email')->nullable(); // Add password column
            $table->rememberToken(); // Adds remember_token column
            $table->timestamp('email_verified_at')->nullable()->after('email'); // Add email_verified_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn(['password', 'remember_token', 'email_verified_at']);
        });
    }
};
