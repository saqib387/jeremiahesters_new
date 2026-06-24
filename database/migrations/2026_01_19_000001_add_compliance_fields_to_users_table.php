<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddComplianceFieldsToUsersTable extends Migration
{
    /**
     * Run the migrations.
     * Adds compliance, KYC/AML, and security fields for legal requirements
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            // Account type - Creator vs Subscriber/Fan
            if (!Schema::hasColumn('users', 'account_type')) {
                $table->enum('account_type', ['subscriber', 'creator'])->default('subscriber')->after('email');
            }
            
            // Phone verification
            if (!Schema::hasColumn('users', 'phone_number')) {
                $table->string('phone_number', 20)->nullable()->after('email');
            }
            if (!Schema::hasColumn('users', 'phone_verified_at')) {
                $table->timestamp('phone_verified_at')->nullable()->after('phone_number');
            }
            if (!Schema::hasColumn('users', 'phone_verification_code')) {
                $table->string('phone_verification_code', 10)->nullable()->after('phone_verified_at');
            }
            
            // Legal compliance - Consent tracking
            if (!Schema::hasColumn('users', 'terms_accepted_at')) {
                $table->timestamp('terms_accepted_at')->nullable()->after('email_verified_at');
            }
            if (!Schema::hasColumn('users', 'privacy_accepted_at')) {
                $table->timestamp('privacy_accepted_at')->nullable()->after('terms_accepted_at');
            }
            if (!Schema::hasColumn('users', 'community_guidelines_accepted_at')) {
                $table->timestamp('community_guidelines_accepted_at')->nullable()->after('privacy_accepted_at');
            }
            if (!Schema::hasColumn('users', 'data_processing_consent_at')) {
                $table->timestamp('data_processing_consent_at')->nullable()->after('community_guidelines_accepted_at');
            }
            if (!Schema::hasColumn('users', 'marketing_consent')) {
                $table->boolean('marketing_consent')->default(false)->after('data_processing_consent_at');
            }
            if (!Schema::hasColumn('users', 'marketing_consent_at')) {
                $table->timestamp('marketing_consent_at')->nullable()->after('marketing_consent');
            }
            
            // Age verification
            if (!Schema::hasColumn('users', 'age_verified_at')) {
                $table->timestamp('age_verified_at')->nullable()->after('birthdate');
            }
            if (!Schema::hasColumn('users', 'age_verification_method')) {
                $table->enum('age_verification_method', ['self_declared', 'id_verified', 'third_party'])->nullable()->after('age_verified_at');
            }
            
            // KYC/AML for crypto
            if (!Schema::hasColumn('users', 'kyc_status')) {
                $table->enum('kyc_status', ['none', 'pending', 'approved', 'rejected', 'expired'])->default('none')->after('age_verification_method');
            }
            if (!Schema::hasColumn('users', 'kyc_level')) {
                $table->tinyInteger('kyc_level')->default(0)->after('kyc_status'); // 0=none, 1=basic, 2=intermediate, 3=full
            }
            if (!Schema::hasColumn('users', 'kyc_verified_at')) {
                $table->timestamp('kyc_verified_at')->nullable()->after('kyc_level');
            }
            if (!Schema::hasColumn('users', 'kyc_expiry_date')) {
                $table->date('kyc_expiry_date')->nullable()->after('kyc_verified_at');
            }
            if (!Schema::hasColumn('users', 'aml_risk_score')) {
                $table->decimal('aml_risk_score', 5, 2)->nullable()->after('kyc_expiry_date');
            }
            if (!Schema::hasColumn('users', 'aml_last_check')) {
                $table->timestamp('aml_last_check')->nullable()->after('aml_risk_score');
            }
            
            // Creator-specific compliance
            if (!Schema::hasColumn('users', 'creator_terms_accepted_at')) {
                $table->timestamp('creator_terms_accepted_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'content_rights_acknowledged_at')) {
                $table->timestamp('content_rights_acknowledged_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'legal_name')) {
                $table->string('legal_name', 255)->nullable();
            }
            if (!Schema::hasColumn('users', 'tax_id')) {
                $table->string('tax_id', 50)->nullable(); // SSN/EIN for US
            }
            if (!Schema::hasColumn('users', 'tax_form_submitted')) {
                $table->boolean('tax_form_submitted')->default(false);
            }
            if (!Schema::hasColumn('users', 'tax_form_type')) {
                $table->string('tax_form_type', 20)->nullable(); // W-9, W-8BEN, etc.
            }
            
            // 2257 Compliance (US Adult Content)
            if (!Schema::hasColumn('users', 'compliance_2257_verified')) {
                $table->boolean('compliance_2257_verified')->default(false);
            }
            if (!Schema::hasColumn('users', 'compliance_2257_verified_at')) {
                $table->timestamp('compliance_2257_verified_at')->nullable();
            }
            
            // Security & Fraud Prevention
            if (!Schema::hasColumn('users', 'login_attempts')) {
                $table->integer('login_attempts')->default(0);
            }
            if (!Schema::hasColumn('users', 'locked_until')) {
                $table->timestamp('locked_until')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'last_login_ip')) {
                $table->string('last_login_ip', 45)->nullable();
            }
            if (!Schema::hasColumn('users', 'registration_ip')) {
                $table->string('registration_ip', 45)->nullable();
            }
            if (!Schema::hasColumn('users', 'fraud_score')) {
                $table->decimal('fraud_score', 5, 2)->default(0);
            }
            if (!Schema::hasColumn('users', 'is_flagged')) {
                $table->boolean('is_flagged')->default(false);
            }
            if (!Schema::hasColumn('users', 'flag_reason')) {
                $table->text('flag_reason')->nullable();
            }
            
            // Session management
            if (!Schema::hasColumn('users', 'session_timeout_minutes')) {
                $table->integer('session_timeout_minutes')->default(60);
            }
            if (!Schema::hasColumn('users', 'force_logout_at')) {
                $table->timestamp('force_logout_at')->nullable();
            }
            
            // Transaction limits for AML
            if (!Schema::hasColumn('users', 'daily_transaction_limit')) {
                $table->decimal('daily_transaction_limit', 20, 8)->default(1000);
            }
            if (!Schema::hasColumn('users', 'monthly_transaction_limit')) {
                $table->decimal('monthly_transaction_limit', 20, 8)->default(10000);
            }
            if (!Schema::hasColumn('users', 'withdrawal_limit')) {
                $table->decimal('withdrawal_limit', 20, 8)->default(500);
            }
            
            // Cookie/GDPR consent
            if (!Schema::hasColumn('users', 'cookie_consent_at')) {
                $table->timestamp('cookie_consent_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'gdpr_consent_at')) {
                $table->timestamp('gdpr_consent_at')->nullable();
            }
            if (!Schema::hasColumn('users', 'data_deletion_requested_at')) {
                $table->timestamp('data_deletion_requested_at')->nullable();
            }
            
            // Social links for creator verification
            if (!Schema::hasColumn('users', 'twitter_url')) {
                $table->string('twitter_url', 255)->nullable();
            }
            if (!Schema::hasColumn('users', 'instagram_url')) {
                $table->string('instagram_url', 255)->nullable();
            }
            if (!Schema::hasColumn('users', 'tiktok_url')) {
                $table->string('tiktok_url', 255)->nullable();
            }
            
            // Wallet connection for crypto
            if (!Schema::hasColumn('users', 'wallet_address')) {
                $table->string('wallet_address', 100)->nullable();
            }
            if (!Schema::hasColumn('users', 'wallet_type')) {
                $table->string('wallet_type', 50)->nullable(); // metamask, coinbase, etc.
            }
            if (!Schema::hasColumn('users', 'wallet_connected_at')) {
                $table->timestamp('wallet_connected_at')->nullable();
            }
        });
        
        // Add indexes for performance
        Schema::table('users', function (Blueprint $table) {
            // Check and add indexes only if they don't exist
            $indexes = collect(\DB::select("SHOW INDEX FROM users"))->pluck('Key_name')->unique();
            
            if (!$indexes->contains('users_account_type_index')) {
                $table->index('account_type');
            }
            if (!$indexes->contains('users_kyc_status_index')) {
                $table->index('kyc_status');
            }
            if (!$indexes->contains('users_is_flagged_index')) {
                $table->index('is_flagged');
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
            $columns = [
                'account_type', 'phone_number', 'phone_verified_at', 'phone_verification_code',
                'terms_accepted_at', 'privacy_accepted_at', 'community_guidelines_accepted_at',
                'data_processing_consent_at', 'marketing_consent', 'marketing_consent_at',
                'age_verified_at', 'age_verification_method',
                'kyc_status', 'kyc_level', 'kyc_verified_at', 'kyc_expiry_date',
                'aml_risk_score', 'aml_last_check',
                'creator_terms_accepted_at', 'content_rights_acknowledged_at',
                'legal_name', 'tax_id', 'tax_form_submitted', 'tax_form_type',
                'compliance_2257_verified', 'compliance_2257_verified_at',
                'login_attempts', 'locked_until', 'last_login_at', 'last_login_ip',
                'registration_ip', 'fraud_score', 'is_flagged', 'flag_reason',
                'session_timeout_minutes', 'force_logout_at',
                'daily_transaction_limit', 'monthly_transaction_limit', 'withdrawal_limit',
                'cookie_consent_at', 'gdpr_consent_at', 'data_deletion_requested_at',
                'twitter_url', 'instagram_url', 'tiktok_url',
                'wallet_address', 'wallet_type', 'wallet_connected_at'
            ];
            
            foreach ($columns as $column) {
                if (Schema::hasColumn('users', $column)) {
                    $table->dropColumn($column);
                }
            }
        });
    }
}
