<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class V240 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Add now payments related column for transactions table
        if (Schema::hasTable('transactions')) {
            Schema::table('transactions', function (Blueprint $table) {
                if (!Schema::hasColumn('transactions', 'nowpayments_payment_id')) {
                    $table->string('nowpayments_payment_id')->after('coinbase_transaction_token')->nullable();
                }
                if (!Schema::hasColumn('transactions', 'nowpayments_order_id')) {
                    $table->string('nowpayments_order_id')->after('nowpayments_payment_id')->nullable();
                }
            });
        }

        if (Schema::hasTable('settings')) {
            $settings = [
                [
                    'key' => 'payments.nowpayments_api_key',
                    'display_name' => 'NowPayments Api Key',
                    'value' => NULL,
                    'details' => NULL,
                    'type' => 'text',
                    'order' => 33,
                    'group' => 'Payments',
                ],
                [
                    'key' => 'payments.nowpayments_ipn_secret_key',
                    'display_name' => 'NowPayments IPN Secret Key',
                    'value' => NULL,
                    'details' => NULL,
                    'type' => 'text',
                    'order' => 34,
                    'group' => 'Payments',
                ]
            ];

            foreach ($settings as $setting) {
                if (!DB::table('settings')->where('key', $setting['key'])->exists()) {
                    DB::table('settings')->insert($setting);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
