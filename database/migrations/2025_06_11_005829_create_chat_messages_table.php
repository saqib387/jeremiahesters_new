<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateChatMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('chat_messages')) {
            Schema::create('chat_messages', function (Blueprint $table) {
                $table->id();
                $table->foreignId('stream_id')->constrained()->onDelete('cascade');
                $table->foreignId('user_id')->nullable()->constrained()->onDelete('set null');
                $table->text('message');
                $table->boolean('is_system')->default(false);
                $table->timestamps();
            });
        }

        if (Schema::hasTable('streams')) {
            Schema::table('streams', function (Blueprint $table) {
                if (Schema::hasColumn('streams', 'sdp_offer')) {
                    $table->dropColumn(['sdp_offer', 'sdp_answer', 'type']);
                }
                if (!Schema::hasColumn('streams', 'stream_key')) {
                    $table->string('stream_key')->nullable()->after('is_live');
                }
                if (!Schema::hasColumn('streams', 'rtmp_url')) {
                    $table->string('rtmp_url')->nullable()->after('stream_key');
                }
                if (!Schema::hasColumn('streams', 'hls_url')) {
                    $table->string('hls_url')->nullable()->after('rtmp_url');
                }
                if (!Schema::hasColumn('streams', 'thumbnail')) {
                    $table->string('thumbnail')->nullable()->after('hls_url');
                }
                if (!Schema::hasColumn('streams', 'status')) {
                    $table->string('status')->default('pending')->after('thumbnail');
                }
                if (!Schema::hasColumn('streams', 'viewer_count')) {
                    $table->integer('viewer_count')->default(0)->after('is_live');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('chat_messages');

        Schema::table('streams', function (Blueprint $table) {
            $table->json('sdp_offer')->nullable();
            $table->json('sdp_answer')->nullable();
            $table->string('type')->default('public');
            $table->dropColumn(['stream_key', 'rtmp_url', 'hls_url', 'thumbnail', 'status', 'viewer_count']);
        });
    }
}
