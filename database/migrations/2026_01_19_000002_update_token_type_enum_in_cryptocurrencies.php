<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

class UpdateTokenTypeEnumInCryptocurrencies extends Migration
{
    /**
     * Run the migrations.
     * Update the token_type enum to include all new token types
     *
     * @return void
     */
    public function up()
    {
        // MySQL requires a raw query to modify ENUM values
        DB::statement("ALTER TABLE cryptocurrencies MODIFY COLUMN token_type ENUM('utility', 'security', 'governance', 'nft', 'payment', 'defi', 'gaming', 'social') DEFAULT 'utility'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // Revert to original enum values
        // Note: This will fail if any rows have the new values
        DB::statement("ALTER TABLE cryptocurrencies MODIFY COLUMN token_type ENUM('utility', 'security', 'governance', 'nft') DEFAULT 'utility'");
    }
}
