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
        Schema::create('support_tickets', function (Blueprint $table) {
            $table->id();
            $table->string('ticket_number')->unique(); // Unique ticket ID
            $table->unsignedBigInteger('user_id'); // User who created the ticket
            $table->unsignedBigInteger('custom_request_id')->nullable(); // Related custom request (if applicable)
            $table->string('type')->default('general'); // 'general', 'dispute', 'payment', 'voting', 'technical'
            $table->string('priority')->default('normal'); // 'low', 'normal', 'high', 'urgent'
            $table->string('status')->default('open'); // 'open', 'in_progress', 'resolved', 'closed', 'escalated'
            $table->string('subject');
            $table->text('description');
            $table->unsignedBigInteger('assigned_to')->nullable(); // Admin/staff assigned
            $table->text('resolution')->nullable(); // Resolution notes
            $table->dateTime('resolved_at')->nullable();
            $table->unsignedBigInteger('resolved_by')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('custom_request_id')->references('id')->on('custom_requests')->onDelete('set null');
            $table->foreign('assigned_to')->references('id')->on('users')->onDelete('set null');
            $table->foreign('resolved_by')->references('id')->on('users')->onDelete('set null');
            
            $table->index(['status', 'priority']);
            $table->index('user_id');
            $table->index('custom_request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('support_tickets');
    }
};
