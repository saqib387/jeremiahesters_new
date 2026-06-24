<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPublicPagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('public_pages', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('slug');
            $table->index('slug');
            $table->string('title');
            $table->string('short_title')->nullable();
            $table->longText('content');
            $table->boolean('is_privacy')->default(false);
            $table->boolean('is_tos')->default(false);
            $table->boolean('shown_in_footer')->default(true);
            $table->integer('page_order')->default(0);
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
        Schema::dropIfExists('public_pages');
    }
}
