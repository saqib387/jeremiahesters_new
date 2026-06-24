<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('custom_request_votes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('custom_request_id');
            $table->unsignedBigInteger('voter_id'); // User who voted
            $table->string('vote_type'); // 'approve', 'reject', 'abstain'
            $table->text('comment')->nullable(); // Optional comment with vote
            $table->boolean('is_requester')->default(false); // Whether voter is the requester
            $table->boolean('is_contributor')->default(false); // Whether voter is a contributor
            $table->decimal('contribution_amount', 10, 2)->default(0); // Amount contributed (for weighted voting if needed)
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('custom_request_id')->references('id')->on('custom_requests')->onDelete('cascade');
            $table->foreign('voter_id')->references('id')->on('users')->onDelete('cascade');
            
            // Ensure one vote per user per request
            $table->unique(['custom_request_id', 'voter_id']);
            $table->index(['custom_request_id', 'vote_type']);
            $table->index('voter_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('custom_request_votes');
    }
};
