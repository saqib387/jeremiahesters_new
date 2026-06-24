<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        // Check if the nowpayments columns already exist in the transactions table
        if (Schema::hasTable('transactions')) {
            if (!Schema::hasColumn('transactions', 'nowpayments_payment_id')) {
                Schema::table('transactions', function (Blueprint $table) {
                    $table->string('nowpayments_payment_id')->nullable();
                    $table->string('nowpayments_order_id')->nullable();
                });
            }
        }

        // Mark the problematic migration as completed
        DB::table('migrations')->insert([
            'migration' => '2116_03_10_185313_v2_4_0',
            'batch' => DB::table('migrations')->max('batch') + 1,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Remove the migration record
        DB::table('migrations')->where('migration', '2116_03_10_185313_v2_4_0')->delete();

        // We don't drop the columns because they might be needed
    }
};
