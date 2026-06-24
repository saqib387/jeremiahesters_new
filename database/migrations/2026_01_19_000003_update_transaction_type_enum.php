<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateTransactionTypeEnum extends Migration
{
    /**
     * Run the migrations.
     * Update the transaction type enum to include deposit, withdraw, create
     *
     * @return void
     */
    public function up()
    {
        // MySQL requires a raw query to modify ENUM values
        DB::statement("ALTER TABLE crypto_transactions MODIFY COLUMN type ENUM('buy', 'sell', 'transfer', 'reward', 'airdrop', 'mint', 'deposit', 'withdraw', 'create', 'swap', 'stake', 'unstake') DEFAULT 'buy'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to original enum values
        DB::statement("ALTER TABLE crypto_transactions MODIFY COLUMN type ENUM('buy', 'sell', 'transfer', 'reward', 'airdrop', 'mint') DEFAULT 'buy'");
    }
}
