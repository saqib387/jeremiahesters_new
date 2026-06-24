<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('data_rows')) {
            return;
        }

        DB::table('data_rows')
            ->where('field', 'identity_verified_at')
            ->where('display_name', 'ID verifed')
            ->update(['display_name' => 'ID verified']);
    }

    public function down(): void
    {
        if (!Schema::hasTable('data_rows')) {
            return;
        }

        DB::table('data_rows')
            ->where('field', 'identity_verified_at')
            ->where('display_name', 'ID verified')
            ->update(['display_name' => 'ID verifed']);
    }
};
