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
        Schema::table('cryptocurrencies', function (Blueprint $table) {
            $table->string('website')->nullable()->after('description');
            $table->string('whitepaper')->nullable()->after('website');
            $table->decimal('liquidity_pool_percentage', 5, 2)->default(20)->after('platform_fee_percentage');
            $table->enum('token_type', ['utility', 'security', 'governance', 'nft'])->default('utility')->after('blockchain_network');
            $table->boolean('enable_burning')->default(false)->after('token_type');
            $table->boolean('enable_minting')->default(false)->after('enable_burning');
            $table->boolean('transferable')->default(true)->after('enable_minting');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cryptocurrencies', function (Blueprint $table) {
            $table->dropColumn([
                'website',
                'whitepaper',
                'liquidity_pool_percentage',
                'token_type',
                'enable_burning',
                'enable_minting',
                'transferable'
            ]);
        });
    }
};
