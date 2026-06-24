<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCryptocurrencyTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Create cryptocurrencies table
        if (!Schema::hasTable('cryptocurrencies')) {
            Schema::create('cryptocurrencies', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->string('symbol', 10);
                $table->string('logo')->nullable();
                $table->text('description')->nullable();
                
                // Price and supply fields (merged from both versions)
                $table->decimal('initial_price', 18, 8)->default(0);
                $table->decimal('current_price', 18, 8)->default(0);
                $table->decimal('market_cap', 24, 2)->default(0);
                $table->decimal('total_supply', 24, 2)->default(0);
                $table->decimal('available_supply', 24, 2)->default(0);
                $table->decimal('circulating_supply', 24, 2)->default(0);
                $table->decimal('max_supply', 24, 2)->nullable();
                $table->decimal('volume_24h', 24, 2)->default(0);
                $table->decimal('change_24h', 8, 4)->default(0); // percentage change
                $table->json('price_history')->nullable();
                
                // Blockchain and contract fields (from new versions)
                $table->string('blockchain_network')->default('ETH');
                $table->string('contract_address')->nullable();
                $table->text('contract_abi')->nullable();
                
                // Creator and fee fields (from new versions)
                $table->unsignedBigInteger('creator_user_id')->nullable();
                $table->decimal('creator_fee_percentage', 5, 2)->default(5.00);
                $table->decimal('platform_fee_percentage', 5, 2)->default(2.50);
                
                // Status fields
                $table->boolean('is_verified')->default(false);
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('creator_user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // Create crypto_wallets table
        if (!Schema::hasTable('crypto_wallets')) {
            Schema::create('crypto_wallets', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('cryptocurrency_id');
                $table->decimal('balance', 24, 8)->default(0);
                $table->string('wallet_address', 64)->nullable();
                $table->text('private_key_encrypted')->nullable();
                $table->boolean('is_active')->default(true);
                $table->timestamps();

                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('cryptocurrency_id')->references('id')->on('cryptocurrencies')->onDelete('cascade');
                $table->unique(['user_id', 'cryptocurrency_id']);
            });
        }

        // Create crypto_transactions table
        if (!Schema::hasTable('crypto_transactions')) {
            Schema::create('crypto_transactions', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('wallet_id')->nullable();
                $table->unsignedBigInteger('cryptocurrency_id');
                $table->unsignedBigInteger('buyer_user_id')->nullable();
                $table->unsignedBigInteger('seller_user_id')->nullable();
                $table->enum('type', ['buy', 'sell', 'transfer', 'reward', 'airdrop', 'mint']);
                $table->decimal('amount', 24, 8);
                $table->decimal('price_per_unit', 18, 8)->nullable();
                $table->decimal('price_per_token', 18, 8)->nullable();
                $table->decimal('total_price', 18, 8)->nullable();
                $table->decimal('fee_amount', 18, 8)->default(0);
                $table->string('from_address', 64)->nullable();
                $table->string('to_address', 64)->nullable();
                $table->unsignedBigInteger('related_user_id')->nullable();
                $table->string('transaction_hash', 64)->unique()->nullable();
                $table->enum('status', ['pending', 'completed', 'failed', 'cancelled'])->default('completed');
                $table->text('notes')->nullable();
                $table->timestamps();

                $table->foreign('wallet_id')->references('id')->on('crypto_wallets')->onDelete('set null');
                $table->foreign('cryptocurrency_id')->references('id')->on('cryptocurrencies')->onDelete('cascade');
                $table->foreign('buyer_user_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('seller_user_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('related_user_id')->references('id')->on('users')->onDelete('set null');
            });
        }

        // Create crypto_revenue_shares table (new table from 2025 versions)
        if (!Schema::hasTable('crypto_revenue_shares')) {
            Schema::create('crypto_revenue_shares', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('cryptocurrency_id');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->unsignedBigInteger('transaction_id')->nullable();
                $table->decimal('percentage', 5, 2)->nullable();
                $table->decimal('revenue_amount', 18, 8)->default(0);
                $table->decimal('distribution_amount', 18, 8)->default(0);
                $table->boolean('is_distributed')->default(false);
                $table->timestamp('distributed_at')->nullable();
                $table->timestamps();

                $table->foreign('cryptocurrency_id')->references('id')->on('cryptocurrencies')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('set null');
                $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
                $table->unique(['cryptocurrency_id', 'user_id'], 'crypto_user_revenue_unique');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('crypto_revenue_shares');
        Schema::dropIfExists('crypto_transactions');
        Schema::dropIfExists('crypto_wallets');
        Schema::dropIfExists('cryptocurrencies');
    }
} 