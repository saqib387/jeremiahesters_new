<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class AddDefaultFollowingList extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // First ensure the user_lists table exists
        if (!Schema::hasTable('user_lists')) {
            Schema::create('user_lists', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->string('name');
                $table->string('type');
                $table->timestamps();
                
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }

        // Add the default following list for the admin user
        $adminUser = DB::table('users')->where('email', 'admin@admin.com')->first();
        if ($adminUser) {
            DB::table('user_lists')->insert([
                'user_id' => $adminUser->id,
                'name' => 'Following',
                'type' => 'following',
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the default following list
        $adminUser = DB::table('users')->where('email', 'admin@admin.com')->first();
        if ($adminUser) {
            DB::table('user_lists')
                ->where('user_id', $adminUser->id)
                ->where('type', 'following')
                ->delete();
        }
    }
} 