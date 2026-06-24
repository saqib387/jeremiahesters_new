<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('streams', function (Blueprint $table) {
            if (!Schema::hasColumn('streams', 'sdp_offer')) {
                $table->json('sdp_offer')->nullable();
            }
            if (!Schema::hasColumn('streams', 'sdp_answer')) {
                $table->json('sdp_answer')->nullable();
            }
        });
    }

    public function down()
    {
        Schema::table('streams', function (Blueprint $table) {
            $table->dropColumn(['sdp_offer', 'sdp_answer']);
        });
    }
}; 