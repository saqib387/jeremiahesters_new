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
        Schema::table('videos', function (Blueprint $table) {
            // Add views_count column if it doesn't exist
            if (!Schema::hasColumn('videos', 'views_count')) {
                $table->unsignedBigInteger('views_count')->default(0)->after('is_public');
            }
            
            // Add likes_count column if it doesn't exist
            if (!Schema::hasColumn('videos', 'likes_count')) {
                $table->unsignedBigInteger('likes_count')->default(0)->after('views_count');
            }
            
            // Add comments_count column if it doesn't exist
            if (!Schema::hasColumn('videos', 'comments_count')) {
                $table->unsignedBigInteger('comments_count')->default(0)->after('likes_count');
            }
            
            // Add shares_count column if it doesn't exist
            if (!Schema::hasColumn('videos', 'shares_count')) {
                $table->unsignedBigInteger('shares_count')->default(0)->after('comments_count');
            }
            
            // Add reposts_count column if it doesn't exist
            if (!Schema::hasColumn('videos', 'reposts_count')) {
                $table->unsignedBigInteger('reposts_count')->default(0)->after('shares_count');
            }
            
            // Add duration column if it doesn't exist
            if (!Schema::hasColumn('videos', 'duration')) {
                $table->unsignedInteger('duration')->nullable()->after('thumbnail_path');
            }
            
            // Add tags column if it doesn't exist
            if (!Schema::hasColumn('videos', 'tags')) {
                $table->json('tags')->nullable()->after('description');
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
        Schema::table('videos', function (Blueprint $table) {
            $columns = ['views_count', 'likes_count', 'comments_count', 'shares_count', 'reposts_count', 'duration', 'tags'];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('videos', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
};
