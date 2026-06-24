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
        Schema::table('streams', function (Blueprint $table) {
            // Add description column if it doesn't exist
            if (!Schema::hasColumn('streams', 'description')) {
                $table->text('description')->nullable()->after('title');
            }
            
            // Add slug column if it doesn't exist
            if (!Schema::hasColumn('streams', 'slug')) {
                $table->string('slug')->unique()->nullable()->after('description');
            }
            
            // Add stream_key column if it doesn't exist
            if (!Schema::hasColumn('streams', 'stream_key')) {
                $table->string('stream_key')->unique()->nullable()->after('slug');
            }
            
            // Add thumbnail column if it doesn't exist
            if (!Schema::hasColumn('streams', 'thumbnail')) {
                $table->string('thumbnail')->nullable()->after('stream_key');
            }
            
            // Add viewer_count column if it doesn't exist
            if (!Schema::hasColumn('streams', 'viewer_count')) {
                $table->unsignedInteger('viewer_count')->default(0)->after('ended_at');
            }
            
            // Add peak_viewer_count column if it doesn't exist
            if (!Schema::hasColumn('streams', 'peak_viewer_count')) {
                $table->unsignedInteger('peak_viewer_count')->default(0)->after('viewer_count');
            }
            
            // Add requires_subscription column if it doesn't exist
            if (!Schema::hasColumn('streams', 'requires_subscription')) {
                $table->boolean('requires_subscription')->default(false)->after('peak_viewer_count');
            }
            
            // Add is_public column if it doesn't exist
            if (!Schema::hasColumn('streams', 'is_public')) {
                $table->boolean('is_public')->default(true)->after('requires_subscription');
            }
            
            // Add price column if it doesn't exist
            if (!Schema::hasColumn('streams', 'price')) {
                $table->decimal('price', 10, 2)->default(0)->after('is_public');
            }
            
            // Add status column if it doesn't exist
            if (!Schema::hasColumn('streams', 'status')) {
                $table->enum('status', ['pending', 'live', 'ended'])->default('pending')->after('price');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('streams', function (Blueprint $table) {
            $columns = ['description', 'slug', 'stream_key', 'thumbnail', 'viewer_count', 
                       'peak_viewer_count', 'requires_subscription', 'is_public', 'price', 'status'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('streams', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
