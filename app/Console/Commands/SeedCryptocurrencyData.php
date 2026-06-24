<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SeedCryptocurrencyData extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'seed:cryptocurrency';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Seed sample cryptocurrency data for testing';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Starting cryptocurrency sample data insertion...');

        // Check if we have any users
        $user = User::first();
        if (!$user) {
            $this->error('Error: No users found in the database. Please create a user first.');
            return 1;
        }

        $this->info("Using user: {$user->name} (ID: {$user->id}) as the creator");

        // Check if we should use App\Models or App\Model namespace
        $modelsNS = class_exists('App\\Models\\Cryptocurrency') ? 'App\\Models\\' : 'App\\Model\\';
        $this->info("Using models namespace: {$modelsNS}");
        
        $cryptoClass = $modelsNS . 'Cryptocurrency';
        $walletClass = $modelsNS . 'CryptoWallet';
        $transactionClass = $modelsNS . 'CryptoTransaction';

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
                $existingCrypto = $cryptoClass::where('symbol', $cryptoData['symbol'])->first();
                if ($existingCrypto) {
                    $this->warn("Cryptocurrency with symbol {$cryptoData['symbol']} already exists. Skipping...");
                    continue;
                }
                
                $this->info("Creating cryptocurrency: {$cryptoData['name']} ({$cryptoData['symbol']})...");
                
                // Create the cryptocurrency
                $crypto = new $cryptoClass();
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
                
                $this->info("Cryptocurrency {$cryptoData['name']} created successfully.");
                
                // Create a wallet for the creator
                $creatorWallet = new $walletClass();
                $creatorWallet->user_id = $user->id;
                $creatorWallet->cryptocurrency_id = $crypto->id;
                $creatorWallet->balance = $crypto->total_supply * 0.1; // Creator gets 10% of total supply
                $creatorWallet->wallet_address = '0x' . Str::random(40);
                $creatorWallet->save();
                
                $this->info("Created wallet for {$user->name} with balance: {$creatorWallet->balance} {$crypto->symbol}");
                
                // Create some transactions if transaction class has defined types
                if (defined("$transactionClass::BUY_TYPE")) {
                    $transactionTypes = [
                        $transactionClass::BUY_TYPE,
                        $transactionClass::SELL_TYPE,
                        $transactionClass::TRANSFER_TYPE,
                        $transactionClass::MINT_TYPE,
                        $transactionClass::REWARD_TYPE
                    ];
                    
                    // Create 5-10 random transactions
                    $numTransactions = rand(5, 10);
                    $this->info("Creating {$numTransactions} sample transactions for {$crypto->symbol}...");
                    
                    $progressBar = $this->output->createProgressBar($numTransactions);
                    $progressBar->start();
                    
                    for ($i = 0; $i < $numTransactions; $i++) {
                        $type = $transactionTypes[array_rand($transactionTypes)];
                        $amount = rand(100, 1000);
                        $pricePerToken = $crypto->current_price * (rand(80, 120) / 100); // Random price fluctuation
                        $totalPrice = $amount * $pricePerToken;
                        $feeAmount = $totalPrice * ($crypto->platform_fee_percentage / 100);
                        
                        // Create transaction
                        $transaction = new $transactionClass();
                        $transaction->cryptocurrency_id = $crypto->id;
                        $transaction->buyer_user_id = $user->id;
                        $transaction->seller_user_id = ($type == $transactionClass::SELL_TYPE) ? null : $user->id;
                        $transaction->type = $type;
                        $transaction->amount = $amount;
                        $transaction->price_per_token = $pricePerToken;
                        $transaction->total_price = $totalPrice;
                        $transaction->fee_amount = $feeAmount;
                        $transaction->transaction_hash = '0x' . Str::random(64);
                        $transaction->status = $transactionClass::COMPLETED_STATUS;
                        $transaction->created_at = now()->subDays(rand(1, 30));
                        $transaction->save();
                        
                        $progressBar->advance();
                    }
                    
                    $progressBar->finish();
                    $this->line('');
                    $this->info("Created {$numTransactions} transactions for {$crypto->symbol}");
                } else {
                    $this->warn("Transaction types not defined in $transactionClass, skipping transaction creation");
                }
            }
            
            // Commit transaction
            DB::commit();
            $this->info('Sample cryptocurrency data inserted successfully!');
            $this->info('You can now visit your cryptocurrency pages at: /cryptocurrency/1');
            
            return 0;
        } catch (\Exception $e) {
            // Roll back transaction
            DB::rollBack();
            $this->error("Error: " . $e->getMessage());
            $this->error("Stack trace: " . $e->getTraceAsString());
            
            return 1;
        }
    }
} 