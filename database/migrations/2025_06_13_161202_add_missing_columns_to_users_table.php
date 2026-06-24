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
        Schema::table('users', function (Blueprint $table) {
            if (!Schema::hasColumn('users', 'enable_2fa')) {
                $table->boolean('enable_2fa')->default(false);
            }
            if (!Schema::hasColumn('users', 'auth_provider')) {
                $table->string('auth_provider')->nullable();
            }
            if (!Schema::hasColumn('users', 'auth_provider_id')) {
                $table->string('auth_provider_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'enable_geoblocking')) {
                $table->boolean('enable_geoblocking')->default(false);
            }
            if (!Schema::hasColumn('users', 'open_profile')) {
                $table->boolean('open_profile')->default(false);
            }
            if (!Schema::hasColumn('users', 'referral_code')) {
                $table->string('referral_code')->nullable();
            }
            if (!Schema::hasColumn('users', 'country_id')) {
                $table->unsignedBigInteger('country_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'paid_profile')) {
                $table->boolean('paid_profile')->default(true);
            }
            if (!Schema::hasColumn('users', 'gender_id')) {
                $table->unsignedBigInteger('gender_id')->nullable();
            }
            if (!Schema::hasColumn('users', 'gender_pronoun')) {
                $table->string('gender_pronoun')->nullable();
            }
            if (!Schema::hasColumn('users', 'profile_access_price_3_months')) {
                $table->float('profile_access_price_3_months')->default(5);
            }
            if (!Schema::hasColumn('users', 'settings')) {
                $table->text('settings')->nullable();
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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'enable_2fa',
                'auth_provider',
                'auth_provider_id',
                'enable_geoblocking',
                'open_profile',
                'referral_code',
                'country_id',
                'paid_profile',
                'gender_id',
                'gender_pronoun',
                'profile_access_price_3_months',
                'settings'
            ]);
        });
    }
};
