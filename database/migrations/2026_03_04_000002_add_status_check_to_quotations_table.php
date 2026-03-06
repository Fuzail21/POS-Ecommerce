<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    // Valid quotation status values used throughout the application
    private const VALID_STATUSES = ['pending', 'sent', 'accepted', 'rejected', 'converted'];

    public function up(): void
    {
        // Normalise any old capitalised seeder values to lowercase before adding constraint
        DB::table('quotations')->whereIn('status', ['Pending', 'Sent', 'Accepted', 'Rejected'])
            ->update(['status' => DB::raw('LOWER(status)')]);

        // Add a CHECK constraint so the DB rejects invalid values going forward
        DB::statement("ALTER TABLE quotations ADD CONSTRAINT chk_quotations_status
            CHECK (status IN ('pending','sent','accepted','rejected','converted'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE quotations DROP CHECK chk_quotations_status');
    }
};
