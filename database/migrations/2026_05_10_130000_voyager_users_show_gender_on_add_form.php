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
            ->where('field', 'user_belongsto_user_gender_relationship')
            ->update(['add' => 1]);
    }

    public function down(): void
    {
        if (!Schema::hasTable('data_rows')) {
            return;
        }

        DB::table('data_rows')
            ->where('field', 'user_belongsto_user_gender_relationship')
            ->update(['add' => 0]);
    }
};
