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
        Schema::table('posts', function (Blueprint $table) {
            if (!Schema::hasColumn('posts', 'release_date')) {
                $table->timestamp('release_date')->nullable();
            }
            if (!Schema::hasColumn('posts', 'expire_date')) {
                $table->timestamp('expire_date')->nullable();
            }
            if (!Schema::hasColumn('posts', 'is_pinned')) {
                $table->boolean('is_pinned')->default(false);
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
        Schema::table('posts', function (Blueprint $table) {
            $table->dropColumn([
                'release_date',
                'expire_date',
                'is_pinned'
            ]);
        });
    }
};
