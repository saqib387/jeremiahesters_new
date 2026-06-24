
<?php

// Sample cryptocurrency data script
// Run this file to populate your database with sample cryptocurrency data

// First, get access to Laravel app
require_once __DIR__ . '/Script/vendor/autoload.php';

$app = require_once __DIR__ . '/Script/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Cryptocurrency;
use App\Models\CryptoTransaction;
use App\Models\CryptoWallet;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

echo "Starting cryptocurrency sample data insertion...\n";

// Check if we have any users
$user = User::first();
if (!$user) {
    die("Error: No users found in the database. Please create a user first.\n");
}

echo "Using user: {$user->name} (ID: {$user->id}) as the creator\n";

// Begin transaction
DB::beginTransaction();

try {
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
            'description' => 'ContentCreator Token (CCT) is designed for content creators. Holders can participate in governance and earn revenue share from platform fees.',
            'initial_price' => 0.05,
            'current_price' => 0.08,
            'total_supply' => 500000,
            'available_supply' => 350000,
            'blockchain_network' => 'ethereum',
            'logo' => null,
            'website' => 'https://example.com/cct',
            'whitepaper' => 'https://example.com/cct/whitepaper',
            'creator_fee_percentage' => 7.50,
            'platform_fee_percentage' => 2.00,
            'liquidity_pool_percentage' => 30.00,
            'token_type' => 'governance',
            'enable_burning' => true,
            'enable_minting' => true,
            'transferable' => true,
            'is_verified' => true,
            'is_active' => true,
        ],
        [
            'name' => 'FanCoin',
            'symbol' => 'FAN',
            'description' => 'FanCoin is a social token that rewards fans for their engagement and loyalty. Use it to access exclusive content and experiences.',
            'initial_price' => 0.001,
            'current_price' => 0.0025,
            'total_supply' => 10000000,
            'available_supply' => 8000000,
            'blockchain_network' => 'polygon',
            'logo' => null,
            'website' => 'https://example.com/fancoin',
            'whitepaper' => 'https://example.com/fancoin/whitepaper',
            'creator_fee_percentage' => 6.00,
            'platform_fee_percentage' => 1.50,
            'liquidity_pool_percentage' => 15.00,
            'token_type' => 'utility',
            'enable_burning' => false,
            'enable_minting' => true,
            'transferable' => true,
            'is_verified' => false,
            'is_active' => true,
        ]
    ];

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
        
        echo "Cryptocurrency {$cryptoData['name']} created successfully.\n";
        
        // Create a wallet for the creator
        $creatorWallet = new CryptoWallet();
        $creatorWallet->user_id = $user->id;
        $creatorWallet->cryptocurrency_id = $crypto->id;
        $creatorWallet->balance = $crypto->total_supply * 0.1; // Creator gets 10% of total supply
        $creatorWallet->wallet_address = '0x' . Str::random(40);
        $creatorWallet->save();
        
        echo "Created wallet for {$user->name} with balance: {$creatorWallet->balance} {$crypto->symbol}\n";
        
        // Create some transactions
        $transactionTypes = [
            CryptoTransaction::BUY_TYPE,
            CryptoTransaction::SELL_TYPE,
            CryptoTransaction::TRANSFER_TYPE,
            CryptoTransaction::MINT_TYPE,
            CryptoTransaction::REWARD_TYPE
        ];
        
        // Create 5-10 random transactions
        $numTransactions = rand(5, 10);
        echo "Creating {$numTransactions} sample transactions for {$crypto->symbol}...\n";
        
        for ($i = 0; $i < $numTransactions; $i++) {
            $type = $transactionTypes[array_rand($transactionTypes)];
            $amount = rand(100, 1000);
            $pricePerToken = $crypto->current_price * (rand(80, 120) / 100); // Random price fluctuation
            $totalPrice = $amount * $pricePerToken;
            $feeAmount = $totalPrice * ($crypto->platform_fee_percentage / 100);
            
            // Create transaction
            $transaction = new CryptoTransaction();
            $transaction->cryptocurrency_id = $crypto->id;
            $transaction->buyer_user_id = $user->id;
            $transaction->seller_user_id = ($type == CryptoTransaction::SELL_TYPE) ? null : $user->id;
            $transaction->type = $type;
            $transaction->amount = $amount;
            $transaction->price_per_token = $pricePerToken;
            $transaction->total_price = $totalPrice;
            $transaction->fee_amount = $feeAmount;
            $transaction->transaction_hash = '0x' . Str::random(64);
            $transaction->status = CryptoTransaction::COMPLETED_STATUS;
            $transaction->created_at = now()->subDays(rand(1, 30));
            $transaction->save();
        }
        
        echo "Created {$numTransactions} transactions for {$crypto->symbol}\n";
    }
    
    // Commit transaction
    DB::commit();
    echo "Sample cryptocurrency data inserted successfully!\n";
    
} catch (\Exception $e) {
    // Roll back transaction
    DB::rollBack();
    echo "Error: " . $e->getMessage() . "\n";
    echo "Stack trace: " . $e->getTraceAsString() . "\n";
} 