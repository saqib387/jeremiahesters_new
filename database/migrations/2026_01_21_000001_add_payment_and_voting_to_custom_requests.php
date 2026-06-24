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
        Schema::table('custom_requests', function (Blueprint $table) {
            // Payment fields
            $table->decimal('upfront_payment', 10, 2)->default(0)->after('price');
            $table->unsignedBigInteger('payment_transaction_id')->nullable()->after('upfront_payment');
            $table->boolean('payment_received')->default(false)->after('payment_transaction_id');
            $table->dateTime('payment_received_at')->nullable()->after('payment_received');
            
            // Voting fields
            $table->boolean('requires_voting')->default(false)->after('is_marketplace');
            $table->integer('total_votes')->default(0)->after('requires_voting');
            $table->integer('approval_votes')->default(0)->after('total_votes');
            $table->integer('rejection_votes')->default(0)->after('approval_votes');
            $table->decimal('approval_percentage', 5, 2)->default(0)->after('rejection_votes');
            $table->boolean('funds_released')->default(false)->after('approval_percentage');
            $table->dateTime('funds_released_at')->nullable()->after('funds_released');
            $table->text('release_notes')->nullable()->after('funds_released_at');
            
            // Support fields
            $table->boolean('has_support_ticket')->default(false)->after('release_notes');
            $table->string('support_status')->nullable()->after('has_support_ticket'); // 'open', 'resolved', 'escalated'
            
            $table->foreign('payment_transaction_id')->references('id')->on('transactions')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('custom_requests', function (Blueprint $table) {
            $table->dropForeign(['payment_transaction_id']);
            $table->dropColumn([
                'upfront_payment',
                'payment_transaction_id',
                'payment_received',
                'payment_received_at',
                'requires_voting',
                'total_votes',
                'approval_votes',
                'rejection_votes',
                'approval_percentage',
                'funds_released',
                'funds_released_at',
                'release_notes',
                'has_support_ticket',
                'support_status',
            ]);
        });
    }
};
