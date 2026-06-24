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
        Schema::table('attachments', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('attachments', 'file_size')) {
                $table->bigInteger('file_size')->nullable();
            }
            
            if (!Schema::hasColumn('attachments', 'mime_type')) {
                $table->string('mime_type')->nullable();
            }
            
            if (!Schema::hasColumn('attachments', 'has_thumbnail')) {
                $table->boolean('has_thumbnail')->default(false);
            }
            
            if (!Schema::hasColumn('attachments', 'driver')) {
                $table->integer('driver')->default(0);
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
            $table->dropColumn(['file_size', 'mime_type', 'has_thumbnail', 'driver']);
        });
    }
};
