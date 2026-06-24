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
        Schema::create('nfts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id'); // Creator/Owner
            $table->string('token_id')->unique(); // Blockchain token ID
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('token_uri'); // IPFS or HTTP URI
            $table->string('image_url')->nullable(); // Display image
            $table->string('contract_address')->nullable(); // Smart contract address
            $table->string('collection_name')->nullable();
            $table->json('metadata')->nullable(); // Additional metadata
            $table->enum('status', ['minted', 'listed', 'sold', 'transferred'])->default('minted');
            $table->timestamps();
            
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->index('token_id');
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nfts');
    }
};
