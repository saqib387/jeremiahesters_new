<?php

/**
 * Sample Cryptocurrency Seeder Script
 * 
 * This script inserts sample cryptocurrency data into your database.
 * Run this script from the terminal: php crypto-sample-seeder.php
 */

// Bootstrap the Laravel application
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\Cryptocurrency;
use App\Models\CryptoWallet;
use App\Models\CryptoTransaction;
use App\User;

// Function to create sample transactions
function createSampleTransactions($crypto, $user) 
{
    echo "Creating sample transactions for {$crypto->symbol}...\n";
    
    // Create some buy transactions
    $numTransactions = rand(3, 8);
    
    for ($i = 0; $i < $numTransactions; $i++) {
        $amount = rand(1000, 5000);
        $pricePerToken = $crypto->current_price * (1 + (rand(-10, 10) / 100)); // Random price variation
        $totalPrice = $amount * $pricePerToken;
        $daysAgo = rand(1, 10);
        
        $transaction = new CryptoTransaction();
        $transaction->cryptocurrency_id = $crypto->id;
        $transaction->buyer_user_id = $user->id;
        $transaction->seller_user_id = null;
        $transaction->transaction_type = 'buy';
        $transaction->amount = $amount;
        $transaction->price_per_token = $pricePerToken;
        $transaction->total_price = $totalPrice;
        $transaction->status = 'completed';
        $transaction->created_at = now()->subDays($daysAgo);
        $transaction->updated_at = $transaction->created_at;
        $transaction->save();
        
        echo "  - Created transaction: {$amount} {$crypto->symbol} @ {$pricePerToken} ({$daysAgo} days ago)\n";
    }
}

try {
    // Get the first user
    $user = User::first();
    
    if (!$user) {
        echo "No users found. Please create a user first.\n";
        exit;
    }
    
    echo "Using user: {$user->name} (ID: {$user->id})\n";
    
    // Sample cryptocurrencies data
    $cryptos = [
        [
            'name' => 'JustCoin',
            'symbol' => 'JCOIN',
            'description' => 'JustCoin is a utility token for the platform. It can be used to purchase premium content, subscribe to creators, and reward high-quality content.',
            'initial_price' => 0.01,
            'current_price' => 0.015,
            'total_supply' => 1000000,
            'available_supply' => 800000,
            'blockchain_network' => 'binance',
            'logo' => null, // No logo initially
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
            'description' => 'ContentCreator Token is designed for content creators to monetize their content and engage with their audience directly. Holders can participate in governance decisions.',
            'initial_price' => 0.05,
            'current_price' => 0.075,
            'total_supply' => 500000,
            'available_supply' => 400000,
            'blockchain_network' => 'ethereum',
            'logo' => null,
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
            'description' => 'FanCoin represents ownership in exclusive fan communities. By holding FanCoins, users get access to exclusive content, merchandise, and events.',
            'initial_price' => 0.02,
            'current_price' => 0.018,
            'total_supply' => 2000000,
            'available_supply' => 1700000,
            'blockchain_network' => 'polygon',
            'logo' => null,
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
    
    DB::beginTransaction();
    
    foreach ($cryptos as $index => $cryptoData) {
        // Check if cryptocurrency already exists
        $existingCrypto = Cryptocurrency::where('symbol', $cryptoData['symbol'])->first();
        if ($existingCrypto) {
            echo "Cryptocurrency with symbol {$cryptoData['symbol']} already exists. Skipping...\n";
            continue;
        }
        
        echo "Creating cryptocurrency: {$cryptoData['name']} ({$cryptoData['symbol']})...\n";
        
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
        
        $crypto->save();
        
        // Create creator wallet
        $wallet = new CryptoWallet();
        $wallet->user_id = $user->id;
        $wallet->cryptocurrency_id = $crypto->id;
        $wallet->balance = $crypto->total_supply * 0.1; // 10% allocation to creator
        $wallet->save();
        
        // Create sample transactions
        createSampleTransactions($crypto, $user);
    }
    
    DB::commit();
    echo "Successfully inserted sample cryptocurrency data!\n";
    echo "You can now visit your cryptocurrency pages at: http://localhost:8000/cryptocurrency/1\n";
    
} catch (Exception $e) {
    DB::rollback();
    echo "Error: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString() . "\n";
} 