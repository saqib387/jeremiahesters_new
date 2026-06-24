<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('video_shares')) {
            Schema::create('video_shares', function (Blueprint $table) {
                $table->id();
                $table->foreignId('video_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->constrained()->onDelete('cascade');
                $table->string('platform');
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('video_shares');
    }
}; 