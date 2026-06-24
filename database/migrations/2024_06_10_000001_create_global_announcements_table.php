<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGlobalAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('global_announcements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->longText('content');
            $table->boolean('is_published')->default(1);
            $table->boolean('is_dismissible')->default(1);
            $table->boolean('is_sticky')->default(0);
            $table->boolean('is_global')->default(0);
            $table->string('size')->default('normal');
            $table->dateTime('expiring_at')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('global_announcements');
    }
} 