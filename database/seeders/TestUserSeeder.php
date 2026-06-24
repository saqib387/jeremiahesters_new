<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class TestUserSeeder extends Seeder
{
    public function run()
    {
        $now = now();
        $roleId = DB::table('roles')->where('name', 'user')->value('id') ?: 2;

        $user = [
            'name' => 'Test Creator',
            'email' => 'creator@example.com',
            'username' => 'testcreator',
            'password' => Hash::make('password'),
            'role_id' => $roleId,
            'email_verified_at' => $now,
            'birthdate' => '1995-01-01',
            'public_profile' => true,
            'profile_access_price' => 0,
            'profile_access_price_6_months' => 0,
            'profile_access_price_12_months' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ];

        $optionalColumns = [
            'account_type' => 'creator',
            'phone_verified_at' => $now,
            'terms_accepted_at' => $now,
            'privacy_accepted_at' => $now,
            'community_guidelines_accepted_at' => $now,
            'data_processing_consent_at' => $now,
            'age_verified_at' => $now,
            'age_verification_method' => 'self_declared',
            'kyc_status' => 'approved',
            'kyc_level' => 2,
            'kyc_verified_at' => $now,
            'creator_terms_accepted_at' => $now,
            'content_rights_acknowledged_at' => $now,
            'legal_name' => 'Test Creator',
            'compliance_2257_verified' => true,
            'compliance_2257_verified_at' => $now,
            'paid_profile' => false,
            'open_profile' => true,
            'credit' => 1000,
        ];

        foreach ($optionalColumns as $column => $value) {
            if (Schema::hasColumn('users', $column)) {
                $user[$column] = $value;
            }
        }

        DB::table('users')->updateOrInsert(
            ['email' => 'creator@example.com'],
            $user
        );

        $userId = DB::table('users')->where('email', 'creator@example.com')->value('id');

        if (!$userId || !Schema::hasTable('user_verifies')) {
            return;
        }

        DB::table('user_verifies')->updateOrInsert(
            ['user_id' => $userId],
            [
                'files' => json_encode([]),
                'status' => 'verified',
                'rejectionReason' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ]
        );

        if (Schema::hasColumn('users', 'identity_verified_at')) {
            DB::table('users')
                ->where('id', $userId)
                ->update(['identity_verified_at' => $now]);
        }
    }
}
