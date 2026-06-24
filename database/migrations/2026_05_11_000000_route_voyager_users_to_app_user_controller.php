<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('data_types')) {
            return;
        }

        DB::table('data_types')
            ->where('slug', 'users')
            ->where('controller', 'TCG\\Voyager\\Http\\Controllers\\VoyagerUserController')
            ->update(['controller' => 'App\\Http\\Controllers\\Voyager\\VoyagerUserController']);
    }

    public function down(): void
    {
        if (! Schema::hasTable('data_types')) {
            return;
        }

        DB::table('data_types')
            ->where('slug', 'users')
            ->where('controller', 'App\\Http\\Controllers\\Voyager\\VoyagerUserController')
            ->update(['controller' => 'TCG\\Voyager\\Http\\Controllers\\VoyagerUserController']);
    }
};
