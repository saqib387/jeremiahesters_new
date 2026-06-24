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
        Schema::create('nft_listings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('nft_id');
            $table->unsignedBigInteger('seller_id'); // User who listed
            $table->string('token_id'); // Blockchain token ID
            $table->decimal('price', 18, 8); // Price in ETH/Wei
            $table->decimal('listing_price', 18, 8)->nullable(); // Marketplace fee
            $table->enum('status', ['active', 'sold', 'cancelled'])->default('active');
            $table->timestamp('listed_at')->useCurrent();
            $table->timestamp('sold_at')->nullable();
            $table->string('transaction_hash')->nullable(); // Blockchain transaction
            $table->timestamps();
            
            $table->foreign('nft_id')->references('id')->on('nfts')->onDelete('cascade');
            $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('token_id');
            $table->index('seller_id');
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
        Schema::dropIfExists('nft_listings');
    }
};
