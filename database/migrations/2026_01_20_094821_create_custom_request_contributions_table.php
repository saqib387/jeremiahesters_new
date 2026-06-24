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
        Schema::create('custom_request_contributions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('custom_request_id');
            $table->unsignedBigInteger('contributor_id'); // User who contributed
            $table->decimal('amount', 10, 2);
            $table->unsignedBigInteger('transaction_id')->nullable(); // Link to payment transaction
            $table->string('status')->default('pending'); // 'pending', 'completed', 'refunded'
            $table->text('message')->nullable(); // Optional message with contribution
            $table->timestamps();

            $table->foreign('custom_request_id')->references('id')->on('custom_requests')->onDelete('cascade');
            $table->foreign('contributor_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('transaction_id')->references('id')->on('transactions')->onDelete('set null');
            
            $table->index(['custom_request_id', 'status']);
            $table->index('contributor_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_request_contributions');
    }
};
