<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMissingColumnsToAttachmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('attachments', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('attachments', 'file_size')) {
                $table->bigInteger('file_size')->nullable()->after('filename');
            }
            
            if (!Schema::hasColumn('attachments', 'mime_type')) {
                $table->string('mime_type')->nullable()->after('type');
            }
            
            if (!Schema::hasColumn('attachments', 'has_thumbnail')) {
                $table->boolean('has_thumbnail')->default(false)->after('driver');
            }
            
            if (!Schema::hasColumn('attachments', 'coconut_id')) {
                $table->string('coconut_id')->nullable()->after('has_thumbnail');
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
        Schema::table('attachments', function (Blueprint $table) {
            // Remove columns if they exist
            if (Schema::hasColumn('attachments', 'file_size')) {
                $table->dropColumn('file_size');
            }
            
            if (Schema::hasColumn('attachments', 'mime_type')) {
                $table->dropColumn('mime_type');
            }
            
            if (Schema::hasColumn('attachments', 'has_thumbnail')) {
                $table->dropColumn('has_thumbnail');
            }
            
            if (Schema::hasColumn('attachments', 'coconut_id')) {
                $table->dropColumn('coconut_id');
            }
        });
    }
} 