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
        Schema::create('nft_transactions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nft_id');
            $table->unsignedBigInteger('listing_id')->nullable();
            $table->unsignedBigInteger('seller_id')->nullable();
            $table->unsignedBigInteger('buyer_id')->nullable();
            $table->string('token_id');
            $table->enum('type', ['mint', 'list', 'sale', 'resale', 'transfer', 'cancel']);
            $table->decimal('price', 18, 8)->nullable();
            $table->decimal('fee', 18, 8)->nullable(); // Marketplace fee
            $table->string('transaction_hash')->unique(); // Blockchain transaction hash
            $table->string('from_address')->nullable();
            $table->string('to_address')->nullable();
            $table->integer('block_number')->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->enum('status', ['pending', 'confirmed', 'failed'])->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('nft_id')->references('id')->on('nfts')->onDelete('cascade');
            $table->foreign('listing_id')->references('id')->on('nft_listings')->onDelete('set null');
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('buyer_id')->references('id')->on('users')->onDelete('set null');
            $table->index('token_id');
            $table->index('transaction_hash');
            $table->index('type');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nft_transactions');
    }
};
