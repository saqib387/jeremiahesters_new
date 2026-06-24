<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_devices', function (Blueprint $table) {
            if (!Schema::hasColumn('user_devices', 'user_id')) {
                $table->unsignedBigInteger('user_id')->after('id');
            }
            
            if (!Schema::hasColumn('user_devices', 'ip')) {
                $table->string('ip')->nullable()->after('user_id');
            }
            
            if (!Schema::hasColumn('user_devices', 'agent')) {
                $table->text('agent')->nullable()->after('ip');
            }
            
            if (!Schema::hasColumn('user_devices', 'device_id')) {
                $table->string('device_id')->nullable()->after('agent');
            }
            
            if (!Schema::hasColumn('user_devices', 'verified_at')) {
                $table->timestamp('verified_at')->nullable()->after('device_id');
            }
            
            if (!Schema::hasColumn('user_devices', 'last_login')) {
                $table->timestamp('last_login')->nullable()->after('verified_at');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_devices', function (Blueprint $table) {
            // No need to drop columns as they might be used by other parts of the application
        });
    }
};
