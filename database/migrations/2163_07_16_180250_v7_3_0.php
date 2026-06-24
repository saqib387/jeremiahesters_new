<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class V730 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (Schema::hasTable('wallets') && Schema::hasColumn('wallets', 'total')) {
            Schema::table('wallets', function (Blueprint $table) {
                $table->float('total', 12)->nullable()->change();
            });
        }

        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'amount')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->float('amount', 12)->nullable()->change();
            });
        }

        if (Schema::hasTable('posts') && Schema::hasColumn('posts', 'price')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->float('price', 12)->nullable()->change();
            });
        }

        if (Schema::hasTable('payment_requests') && Schema::hasColumn('payment_requests', 'amount')) {
            Schema::table('payment_requests', function (Blueprint $table) {
                $table->float('amount', 12)->nullable()->change();
            });
        }

        if (Schema::hasTable('subscriptions') && Schema::hasColumn('subscriptions', 'amount')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->float('amount', 12)->nullable()->change();
            });
        }

        if (Schema::hasTable('streams') && Schema::hasColumn('streams', 'price')) {
            Schema::table('streams', function (Blueprint $table) {
                $table->float('price', 12)->nullable()->change();
            });
        }

        if (Schema::hasTable('withdrawals')) {
            Schema::table('withdrawals', function (Blueprint $table) {
                if (Schema::hasColumn('withdrawals', 'fee')) {
                    $table->float('fee', 12)->nullable()->change();
                }
                if (Schema::hasColumn('withdrawals', 'amount')) {
                    $table->float('amount', 12)->nullable()->change();
                }
            });
        }

        if (Schema::hasTable('user_messages') && Schema::hasColumn('user_messages', 'price')) {
            Schema::table('user_messages', function (Blueprint $table) {
                $table->float('price', 12)->nullable()->change();
            });
        }

        if (Schema::hasTable('rewards') && Schema::hasColumn('rewards', 'amount')) {
            Schema::table('rewards', function (Blueprint $table) {
                $table->float('amount', 12)->nullable()->change();
            });
        }

        if (!DB::table('settings')->where('key', 'ai.open_ai_model')->exists()) {
            DB::table('settings')->insert([
                'key' => 'ai.open_ai_model',
                'display_name' => 'OpenAI Model',
                'value' => 'gpt-3.5-turbo-instruct',
                'details' => '{
"default" : "gpt-3.5-turbo-instruct",
"options" : {
"gpt-4o": "GPT 4.0-o",
"gpt-4": "GPT 4.0",
"gpt-3.5-turbo-instruct": "GPT 3.5 Turbo Instruct"
},
"description" : "The OpenAI model to be used. You can check more details, including pricing at their docs/models page."
}',
                'type' => 'select_dropdown',
                'order' => 22,
                'group' => 'AI',
            ]);
        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('wallets') && Schema::hasColumn('wallets', 'total')) {
            Schema::table('wallets', function (Blueprint $table) {
                $table->float('total')->nullable()->change();
            });
        }

        if (Schema::hasTable('transactions') && Schema::hasColumn('transactions', 'amount')) {
            Schema::table('transactions', function (Blueprint $table) {
                $table->float('amount')->nullable()->change();
            });
        }

        if (Schema::hasTable('posts') && Schema::hasColumn('posts', 'price')) {
            Schema::table('posts', function (Blueprint $table) {
                $table->float('price')->nullable()->change();
            });
        }

        if (Schema::hasTable('payment_requests') && Schema::hasColumn('payment_requests', 'amount')) {
            Schema::table('payment_requests', function (Blueprint $table) {
                $table->float('amount')->nullable()->change();
            });
        }

        if (Schema::hasTable('subscriptions') && Schema::hasColumn('subscriptions', 'amount')) {
            Schema::table('subscriptions', function (Blueprint $table) {
                $table->float('amount')->nullable()->change();
            });
        }

        if (Schema::hasTable('streams') && Schema::hasColumn('streams', 'price')) {
            Schema::table('streams', function (Blueprint $table) {
                $table->float('price')->nullable()->change();
            });
        }

        if (Schema::hasTable('withdrawals')) {
            Schema::table('withdrawals', function (Blueprint $table) {
                if (Schema::hasColumn('withdrawals', 'fee')) {
                    $table->float('fee')->nullable()->change();
                }
                if (Schema::hasColumn('withdrawals', 'amount')) {
                    $table->float('amount')->nullable()->change();
                }
            });
        }

        if (Schema::hasTable('user_messages') && Schema::hasColumn('user_messages', 'price')) {
            Schema::table('user_messages', function (Blueprint $table) {
                $table->float('price')->nullable()->change();
            });
        }

        if (Schema::hasTable('rewards') && Schema::hasColumn('rewards', 'amount')) {
            Schema::table('rewards', function (Blueprint $table) {
                $table->float('amount')->nullable()->change();
            });
        }

        DB::table('settings')
            ->whereIn('key', [
                'ai.open_ai_model',
            ])
            ->delete();

    }
};
