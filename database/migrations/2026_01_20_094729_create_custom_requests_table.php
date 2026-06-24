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
        Schema::create('custom_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('creator_id'); // The creator who will fulfill the request
            $table->unsignedBigInteger('requester_id')->nullable(); // The user making the request (null for public/marketplace)
            $table->string('type')->default('public'); // 'private', 'public', 'marketplace'
            $table->string('title');
            $table->text('description');
            $table->decimal('goal_amount', 10, 2)->default(0); // For marketplace requests
            $table->decimal('current_amount', 10, 2)->default(0); // Current contributions
            $table->decimal('price', 10, 2)->nullable(); // For private/public requests
            $table->string('status')->default('pending'); // 'pending', 'accepted', 'rejected', 'completed', 'cancelled'
            $table->unsignedBigInteger('message_id')->nullable(); // For private requests via messages
            $table->boolean('is_marketplace')->default(false); // Whether it's a marketplace request
            $table->dateTime('deadline')->nullable(); // Optional deadline
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('creator_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('requester_id')->references('id')->on('users')->onDelete('set null');
            $table->foreign('message_id')->references('id')->on('user_messages')->onDelete('set null');
            
            $table->index(['creator_id', 'status']);
            $table->index(['type', 'status']);
            $table->index('is_marketplace');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('custom_requests');
    }
};
