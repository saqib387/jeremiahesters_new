<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Model\Post;
use App\Models\Cryptocurrency;
use App\Models\CryptoWallet;
use App\Models\CryptoTransaction;
use App\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class SampleDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->command->info('Creating sample users...');
        
        // Check if the credit column exists
        $hasCredit = Schema::hasColumn('users', 'credit');
        
        // Create sample users if they don't exist
        $user1 = User::where('email', 'user1@example.com')->first();
        if (!$user1) {
            $userData = [
                'name' => 'Sample User 1',
                'email' => 'user1@example.com',
                'username' => 'sampleuser1',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ];
            
            if ($hasCredit) {
                $userData['credit'] = 1000;
            }
            
            $user1 = User::create($userData);
        }
        
        $user2 = User::where('email', 'user2@example.com')->first();
        if (!$user2) {
            $userData = [
                'name' => 'Sample User 2',
                'email' => 'user2@example.com',
                'username' => 'sampleuser2',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ];
            
            if ($hasCredit) {
                $userData['credit'] = 500;
            }
            
            $user2 = User::create($userData);
        }
        
        $this->command->info('Creating sample posts...');
        
        // Sample post content
        $postContents = [
            'Just posted my first update! #excited',
            'Check out this amazing content I created today.',
            'Happy to share my journey with all of you!',
            'New day, new opportunities. Stay tuned for more!',
            'Thanks for all your support, it means a lot to me.'
        ];
        
        // Create sample posts if needed
        if (Post::count() < 5) {
            foreach ($postContents as $content) {
                Post::create([
                    'user_id' => rand(0, 1) ? $user1->id : $user2->id,
                    'text' => $content,
                    'status' => 1,
                    'price' => rand(0, 1) ? rand(1, 10) : 0,
                ]);
            }
        }
        
        $this->command->info('Creating sample cryptocurrencies...');
        
        // Check if Cryptocurrency model exists
        if (!class_exists('\App\Models\Cryptocurrency')) {
            $this->command->warn('Cryptocurrency model not found. Skipping cryptocurrency creation.');
            return;
        }
        
        // Sample crypto data
        $cryptoData = [
            [
                'name' => 'Sample Coin',
                'symbol' => 'SMPL',
                'description' => 'This is a sample cryptocurrency for testing purposes.',
                'initial_price' => 0.01,
                'total_supply' => 1000000,
            ],
            [
                'name' => 'Test Token',
                'symbol' => 'TEST',
                'description' => 'A test token for the platform.',
                'initial_price' => 0.05,
                'total_supply' => 500000,
            ]
        ];
        
        // Create sample cryptocurrencies if needed
        try {
            if (Cryptocurrency::count() < 2) {
                foreach ($cryptoData as $crypto) {
                    $cryptocurrency = new Cryptocurrency();
                    $cryptocurrency->name = $crypto['name'];
                    $cryptocurrency->symbol = $crypto['symbol'];
                    $cryptocurrency->description = $crypto['description'];
                    $cryptocurrency->initial_price = $crypto['initial_price'];
                    $cryptocurrency->current_price = $crypto['initial_price'];
                    $cryptocurrency->total_supply = $crypto['total_supply'];
                    $cryptocurrency->available_supply = $crypto['total_supply'] * 0.8; // 80% available
                    $cryptocurrency->creator_user_id = $user1->id;
                    $cryptocurrency->blockchain_network = 'ethereum';
                    $cryptocurrency->creator_fee_percentage = 5.0;
                    $cryptocurrency->platform_fee_percentage = 2.5;
                    $cryptocurrency->token_type = 'utility';
                    $cryptocurrency->is_active = true;
                    $cryptocurrency->enable_burning = true;
                    $cryptocurrency->enable_minting = false;
                    $cryptocurrency->transferable = true;
                    $cryptocurrency->contract_address = Str::random(42); // Simulate contract address
                    $cryptocurrency->save();
                    
                    // Create wallet for creator
                    $wallet = new CryptoWallet();
                    $wallet->user_id = $user1->id;
                    $wallet->cryptocurrency_id = $cryptocurrency->id;
                    $wallet->balance = $crypto['total_supply'] * 0.1; // 10% for creator
                    $wallet->save();
                    
                    // Create some sample transactions
                    $transaction = new CryptoTransaction();
                    $transaction->cryptocurrency_id = $cryptocurrency->id;
                    $transaction->buyer_user_id = $user2->id;
                    $transaction->seller_user_id = $user1->id;
                    $transaction->transaction_type = 'buy';
                    $transaction->amount = 1000;
                    $transaction->price_per_token = $crypto['initial_price'];
                    $transaction->total_price = 1000 * $crypto['initial_price'];
                    $transaction->status = 'completed';
                    $transaction->save();
                    
                    // Create wallet for buyer
                    $buyerWallet = CryptoWallet::where('user_id', $user2->id)
                        ->where('cryptocurrency_id', $cryptocurrency->id)
                        ->first();
                        
                    if (!$buyerWallet) {
                        $buyerWallet = new CryptoWallet();
                        $buyerWallet->user_id = $user2->id;
                        $buyerWallet->cryptocurrency_id = $cryptocurrency->id;
                        $buyerWallet->balance = 1000;
                        $buyerWallet->save();
                    } else {
                        $buyerWallet->balance += 1000;
                        $buyerWallet->save();
                    }
                }
            }
        } catch (\Exception $e) {
            $this->command->error('Error creating cryptocurrencies: ' . $e->getMessage());
        }
        
        $this->command->info('Sample data created successfully!');
    }
} 