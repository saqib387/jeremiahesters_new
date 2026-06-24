<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToVideosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('videos', function (Blueprint $table) {
            if (!Schema::hasColumn('videos', 'is_private')) {
                $table->boolean('is_private')->default(false);
            }
            if (!Schema::hasColumn('videos', 'status')) {
                $table->string('status')->default('ready');
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
            if (Schema::hasColumn('videos', 'status')) {
                $table->dropColumn('status');
            }
            if (Schema::hasColumn('videos', 'is_private')) {
                $table->dropColumn('is_private');
            }
        });
    }
}
