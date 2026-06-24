<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Cryptocurrency;
use App\Models\CryptoWallet;
use App\Models\CryptoTransaction;
use App\User;
use Illuminate\Support\Str;

class SampleCryptocurrencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Get first user or admin to be the creator
        $user = User::first();
        
        if (!$user) {
            $this->command->error('No users found in the database. Please create a user first.');
            return;
        }
        
        // Sample cryptocurrencies data
        $cryptos = [
            [
                'name' => 'JustCoin',
                'symbol' => 'JCOIN',
                'description' => 'JustCoin is a utility token for the JustFans platform. It can be used to purchase premium content, subscribe to creators, and reward high-quality content.',
                'initial_price' => 0.01,
                'current_price' => 0.015,
                'total_supply' => 1000000,
                'available_supply' => 800000,
                'blockchain_network' => 'binance',
                'logo' => 'crypto/logos/justcoin.png', // This file should be uploaded to storage
                'website' => 'https://example.com/justcoin',
                'whitepaper' => 'https://example.com/justcoin/whitepaper',
                'creator_fee_percentage' => 5.00,
                'platform_fee_percentage' => 2.50,
                'liquidity_pool_percentage' => 20.00,
                'token_type' => 'utility',
                'enable_burning' => true,
                'enable_minting' => false,
                'transferable' => true,
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'name' => 'ContentCreator Token',
                'symbol' => 'CCT',
                'description' => 'ContentCreator Token is designed for content creators to monetize their content and engage with their audience directly. Holders can participate in governance decisions and receive rewards.',
                'initial_price' => 0.05,
                'current_price' => 0.075,
                'total_supply' => 500000,
                'available_supply' => 400000,
                'blockchain_network' => 'ethereum',
                'logo' => 'crypto/logos/cct.png',
                'website' => 'https://example.com/cct',
                'whitepaper' => 'https://example.com/cct/whitepaper',
                'creator_fee_percentage' => 4.00,
                'platform_fee_percentage' => 2.00,
                'liquidity_pool_percentage' => 15.00,
                'token_type' => 'governance',
                'enable_burning' => false,
                'enable_minting' => true,
                'transferable' => true,
                'is_verified' => true,
                'is_active' => true,
            ],
            [
                'name' => 'FanCoin',
                'symbol' => 'FAN',
                'description' => 'FanCoin represents ownership in exclusive fan communities. By holding FanCoins, users get access to exclusive content, merchandise, and events from their favorite creators.',
                'initial_price' => 0.02,
                'current_price' => 0.018,
                'total_supply' => 2000000,
                'available_supply' => 1700000,
                'blockchain_network' => 'polygon',
                'logo' => 'crypto/logos/fancoin.png',
                'website' => 'https://example.com/fancoin',
                'whitepaper' => 'https://example.com/fancoin/whitepaper',
                'creator_fee_percentage' => 6.00,
                'platform_fee_percentage' => 3.00,
                'liquidity_pool_percentage' => 25.00,
                'token_type' => 'utility',
                'enable_burning' => true,
                'enable_minting' => false,
                'transferable' => true,
                'is_verified' => false,
                'is_active' => true,
            ],
        ];
        
        foreach ($cryptos as $cryptoData) {
            // Create the cryptocurrency
            $crypto = new Cryptocurrency();
            $crypto->creator_user_id = $user->id;
            $crypto->name = $cryptoData['name'];
            $crypto->symbol = $cryptoData['symbol'];
            $crypto->description = $cryptoData['description'];
            $crypto->initial_price = $cryptoData['initial_price'];
            $crypto->current_price = $cryptoData['current_price'];
            $crypto->total_supply = $cryptoData['total_supply'];
            $crypto->available_supply = $cryptoData['available_supply'];
            $crypto->blockchain_network = $cryptoData['blockchain_network'];
            $crypto->logo = $cryptoData['logo'];
            $crypto->website = $cryptoData['website'];
            $crypto->whitepaper = $cryptoData['whitepaper'];
            $crypto->creator_fee_percentage = $cryptoData['creator_fee_percentage'];
            $crypto->platform_fee_percentage = $cryptoData['platform_fee_percentage'];
            $crypto->liquidity_pool_percentage = $cryptoData['liquidity_pool_percentage'];
            $crypto->token_type = $cryptoData['token_type'];
            $crypto->enable_burning = $cryptoData['enable_burning'];
            $crypto->enable_minting = $cryptoData['enable_minting'];
            $crypto->transferable = $cryptoData['transferable'];
            $crypto->is_verified = $cryptoData['is_verified'];
            $crypto->is_active = $cryptoData['is_active'];
            $crypto->contract_address = '0x' . Str::random(40); // Fake contract address
            $crypto->contract_abi = null;
            $crypto->save();
            
            // Create creator wallet
            $wallet = new CryptoWallet();
            $wallet->user_id = $user->id;
            $wallet->cryptocurrency_id = $crypto->id;
            $wallet->balance = $crypto->total_supply * 0.1; // 10% allocation to creator
            $wallet->save();
            
            // Create sample transactions
            $this->createSampleTransactions($crypto, $user);
            
            $this->command->info("Created cryptocurrency: {$crypto->name} ({$crypto->symbol})");
        }
    }
    
    /**
     * Create sample transactions for a cryptocurrency
     *
     * @param Cryptocurrency $crypto
     * @param User $creator
     * @return void
     */
    private function createSampleTransactions($crypto, $creator)
    {
        // Create some buy transactions
        $numTransactions = rand(5, 15);
        
        for ($i = 0; $i < $numTransactions; $i++) {
            $amount = rand(100, 5000);
            $pricePerToken = $crypto->current_price * (1 + (rand(-10, 10) / 100)); // Random price variation
            $totalPrice = $amount * $pricePerToken;
            
            $transaction = new CryptoTransaction();
            $transaction->cryptocurrency_id = $crypto->id;
            $transaction->buyer_user_id = $creator->id;
            $transaction->seller_user_id = null;
            $transaction->transaction_type = 'buy';
            $transaction->amount = $amount;
            $transaction->price_per_token = $pricePerToken;
            $transaction->total_price = $totalPrice;
            $transaction->status = 'completed';
            $transaction->created_at = now()->subHours(rand(1, 240)); // Random time in the past (up to 10 days)
            $transaction->updated_at = $transaction->created_at;
            $transaction->save();
        }
    }
} 