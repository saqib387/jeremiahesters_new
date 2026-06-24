<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DefaultUserListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin = DB::table('users')->where('email', 'admin@admin.com')->first();
        if ($admin) {
            $exists = DB::table('user_lists')->where('user_id', $admin->id)->where('type', 'following')->exists();
            if (!$exists) {
                DB::table('user_lists')->insert([
                    'user_id' => $admin->id,
                    'name' => 'Following',
                    'type' => 'following',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } else {
            Log::warning('Admin user not found for DefaultUserListSeeder.');
        }
    }
} 