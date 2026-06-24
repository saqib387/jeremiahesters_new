<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * user_genders.name does not exist; the column is gender_name. Label "name" yields blank options.
     */
    public function up(): void
    {
        if (!Schema::hasTable('data_rows')) {
            return;
        }

        $row = DB::table('data_rows')
            ->where('field', 'user_belongsto_user_gender_relationship')
            ->first();

        if ($row && $row->details) {
            $details = json_decode($row->details, true);
            if (is_array($details) && ($details['label'] ?? null) === 'name') {
                $details['label'] = 'gender_name';
                DB::table('data_rows')
                    ->where('id', $row->id)
                    ->update(['details' => json_encode($details)]);
            }
        }
    }

    public function down(): void
    {
        if (!Schema::hasTable('data_rows')) {
            return;
        }

        $row = DB::table('data_rows')
            ->where('field', 'user_belongsto_user_gender_relationship')
            ->first();

        if ($row && $row->details) {
            $details = json_decode($row->details, true);
            if (is_array($details) && ($details['label'] ?? null) === 'gender_name') {
                $details['label'] = 'name';
                DB::table('data_rows')
                    ->where('id', $row->id)
                    ->update(['details' => json_encode($details)]);
            }
        }
    }
};
