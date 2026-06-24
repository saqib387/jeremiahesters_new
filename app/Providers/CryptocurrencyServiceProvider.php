<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Cryptocurrency;
use App\Models\CryptoTransaction;
use App\Models\CryptoWallet;
use App\Models\CryptoRevenueShare;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CryptocurrencyServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('cryptocurrency', function ($app) {
            return $this;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Create a new cryptocurrency.
     *
     * @param array $data
     * @return Cryptocurrency|null
     */
    public function createCryptocurrency(array $data)
    {
        try {
            // Create the cryptocurrency
            $cryptocurrency = Cryptocurrency::create([
                'creator_user_id' => $data['creator_user_id'],
                'name' => $data['name'],
                'symbol' => $data['symbol'],
                'logo' => $data['logo'] ?? null,
                'description' => $data['description'] ?? null,
                'website' => $data['website'] ?? null,
                'whitepaper' => $data['whitepaper'] ?? null,
                'initial_price' => $data['initial_price'],
                'current_price' => $data['initial_price'],
                'total_supply' => $data['total_supply'],
                'available_supply' => $data['total_supply'] - ($data['creator_allocation'] ?? 0),
                'blockchain_network' => $data['blockchain_network'],
                'token_type' => $data['token_type'] ?? 'utility',
                'enable_burning' => $data['enable_burning'] ?? false,
                'enable_minting' => $data['enable_minting'] ?? false,
                'transferable' => $data['transferable'] ?? true,
                'creator_fee_percentage' => $data['creator_fee_percentage'] ?? 5.00,
                'platform_fee_percentage' => $data['platform_fee_percentage'] ?? 2.50,
                'liquidity_pool_percentage' => $data['liquidity_pool_percentage'] ?? 20.00,
                'is_verified' => false,
                'is_active' => true
            ]);

            // Deploy smart contract (this would be handled by a Web3 service in production)
            $contractData = $this->deploySmartContract($cryptocurrency);
            
            if ($contractData) {
                $cryptocurrency->contract_address = $contractData['address'];
                $cryptocurrency->contract_abi = $contractData['abi'];
                $cryptocurrency->save();
                
                // Create wallet for creator
                if (isset($data['creator_allocation']) && $data['creator_allocation'] > 0) {
                    $this->createWallet($data['creator_user_id'], $cryptocurrency->id);
                    
                    // Mint tokens for creator
                    $this->mintTokens(
                        $cryptocurrency->id, 
                        $data['creator_user_id'], 
                        $data['creator_allocation']
                    );
                }
                
                return $cryptocurrency;
            }
            
            // Delete cryptocurrency if contract deployment fails
            $cryptocurrency->delete();
            return null;
        } catch (\Exception $e) {
            Log::error('Error creating cryptocurrency: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Deploy a smart contract for the cryptocurrency.
     *
     * @param Cryptocurrency $cryptocurrency
     * @return array|null
     */
    protected function deploySmartContract(Cryptocurrency $cryptocurrency)
    {
        // In a real implementation, this would interact with a Web3 provider
        // For now, we'll simulate the contract deployment
        try {
            // Simulate contract deployment
            $address = '0x' . bin2hex(random_bytes(20));
            
            // Define ABI based on token type and features
            $abiMethods = [
                // Basic methods for all tokens
                [
                    'constant' => true,
                    'inputs' => [],
                    'name' => 'name',
                    'outputs' => [['name' => '', 'type' => 'string']],
                    'payable' => false,
                    'stateMutability' => 'view',
                    'type' => 'function'
                ],
                [
                    'constant' => true,
                    'inputs' => [],
                    'name' => 'symbol',
                    'outputs' => [['name' => '', 'type' => 'string']],
                    'payable' => false,
                    'stateMutability' => 'view',
                    'type' => 'function'
                ],
                [
                    'constant' => true,
                    'inputs' => [],
                    'name' => 'totalSupply',
                    'outputs' => [['name' => '', 'type' => 'uint256']],
                    'payable' => false,
                    'stateMutability' => 'view',
                    'type' => 'function'
                ],
                [
                    'constant' => true,
                    'inputs' => [['name' => 'account', 'type' => 'address']],
                    'name' => 'balanceOf',
                    'outputs' => [['name' => '', 'type' => 'uint256']],
                    'payable' => false,
                    'stateMutability' => 'view',
                    'type' => 'function'
                ]
            ];
            
            // Add transfer method if transferable
            if ($cryptocurrency->transferable) {
                $abiMethods[] = [
                    'constant' => false,
                    'inputs' => [
                        ['name' => 'recipient', 'type' => 'address'],
                        ['name' => 'amount', 'type' => 'uint256']
                    ],
                    'name' => 'transfer',
                    'outputs' => [['name' => '', 'type' => 'bool']],
                    'payable' => false,
                    'stateMutability' => 'nonpayable',
                    'type' => 'function'
                ];
            }
            
            // Add burn method if burning is enabled
            if ($cryptocurrency->enable_burning) {
                $abiMethods[] = [
                    'constant' => false,
                    'inputs' => [['name' => 'amount', 'type' => 'uint256']],
                    'name' => 'burn',
                    'outputs' => [],
                    'payable' => false,
                    'stateMutability' => 'nonpayable',
                    'type' => 'function'
                ];
            }
            
            // Add mint method if minting is enabled
            if ($cryptocurrency->enable_minting) {
                $abiMethods[] = [
                    'constant' => false,
                    'inputs' => [
                        ['name' => 'to', 'type' => 'address'],
                        ['name' => 'amount', 'type' => 'uint256']
                    ],
                    'name' => 'mint',
                    'outputs' => [],
                    'payable' => false,
                    'stateMutability' => 'nonpayable',
                    'type' => 'function'
                ];
            }
            
            // Add governance methods if token type is governance
            if ($cryptocurrency->token_type === 'governance') {
                $abiMethods[] = [
                    'constant' => false,
                    'inputs' => [
                        ['name' => 'proposalId', 'type' => 'uint256'],
                        ['name' => 'support', 'type' => 'bool']
                    ],
                    'name' => 'vote',
                    'outputs' => [],
                    'payable' => false,
                    'stateMutability' => 'nonpayable',
                    'type' => 'function'
                ];
                
                $abiMethods[] = [
                    'constant' => false,
                    'inputs' => [
                        ['name' => 'description', 'type' => 'string']
                    ],
                    'name' => 'propose',
                    'outputs' => [['name' => '', 'type' => 'uint256']],
                    'payable' => false,
                    'stateMutability' => 'nonpayable',
                    'type' => 'function'
                ];
            }
            
            // Add NFT methods if token type is NFT
            if ($cryptocurrency->token_type === 'nft') {
                $abiMethods[] = [
                    'constant' => true,
                    'inputs' => [['name' => 'tokenId', 'type' => 'uint256']],
                    'name' => 'ownerOf',
                    'outputs' => [['name' => '', 'type' => 'address']],
                    'payable' => false,
                    'stateMutability' => 'view',
                    'type' => 'function'
                ];
                
                $abiMethods[] = [
                    'constant' => true,
                    'inputs' => [['name' => 'tokenId', 'type' => 'uint256']],
                    'name' => 'tokenURI',
                    'outputs' => [['name' => '', 'type' => 'string']],
                    'payable' => false,
                    'stateMutability' => 'view',
                    'type' => 'function'
                ];
            }
            
            return [
                'address' => $address,
                'abi' => json_encode($abiMethods)
            ];
        } catch (\Exception $e) {
            Log::error('Error deploying smart contract: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Create or get a wallet for a user and cryptocurrency.
     *
     * @param int $userId
     * @param int $cryptocurrencyId
     * @return CryptoWallet
     */
    public function createWallet(int $userId, int $cryptocurrencyId)
    {
        $wallet = CryptoWallet::firstOrCreate(
            [
                'user_id' => $userId,
                'cryptocurrency_id' => $cryptocurrencyId
            ],
            [
                'balance' => 0,
                'wallet_address' => '0x' . bin2hex(random_bytes(20)),
                // Custodial private keys are no longer stored — the platform is non-custodial
                // (users hold their own keys via their connected wallet).
            ]
        );
        
        return $wallet;
    }

    /**
     * Mint tokens for a user.
     *
     * @param int $cryptocurrencyId
     * @param int $userId
     * @param int $amount
     * @return CryptoTransaction|null
     */
    public function mintTokens(int $cryptocurrencyId, int $userId, int $amount)
    {
        try {
            $cryptocurrency = Cryptocurrency::findOrFail($cryptocurrencyId);
            $wallet = $this->createWallet($userId, $cryptocurrencyId);
            
            // Create mint transaction
            $transaction = CryptoTransaction::create([
                'cryptocurrency_id' => $cryptocurrencyId,
                'buyer_user_id' => $userId,
                'seller_user_id' => null,
                'type' => CryptoTransaction::MINT_TYPE,
                'amount' => $amount,
                'price_per_token' => $cryptocurrency->initial_price,
                'total_price' => $cryptocurrency->initial_price * $amount,
                'fee_amount' => 0,
                'status' => CryptoTransaction::COMPLETED_STATUS
            ]);
            
            // Update wallet balance
            $wallet->balance += $amount;
            $wallet->save();
            
            return $transaction;
        } catch (\Exception $e) {
            Log::error('Error minting tokens: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Buy tokens.
     *
     * @param int $userId
     * @param int $cryptocurrencyId
     * @param int $amount
     * @return CryptoTransaction|null
     */
    public function buyTokens(int $userId, int $cryptocurrencyId, int $amount)
    {
        try {
            $cryptocurrency = Cryptocurrency::findOrFail($cryptocurrencyId);
            $buyerWallet = $this->createWallet($userId, $cryptocurrencyId);
            
            // Calculate fees
            $totalPrice = $cryptocurrency->current_price * $amount;
            $platformFee = $totalPrice * ($cryptocurrency->platform_fee_percentage / 100);
            $creatorFee = $totalPrice * ($cryptocurrency->creator_fee_percentage / 100);
            
            // Create buy transaction
            $transaction = CryptoTransaction::create([
                'cryptocurrency_id' => $cryptocurrencyId,
                'buyer_user_id' => $userId,
                'seller_user_id' => null, // Direct purchase from platform
                'type' => CryptoTransaction::BUY_TYPE,
                'amount' => $amount,
                'price_per_token' => $cryptocurrency->current_price,
                'total_price' => $totalPrice,
                'fee_amount' => $platformFee + $creatorFee,
                'status' => CryptoTransaction::COMPLETED_STATUS
            ]);
            
            // Update buyer wallet balance
            $buyerWallet->balance += $amount;
            $buyerWallet->save();
            
            // Create revenue share for creator
            if ($creatorFee > 0) {
                CryptoRevenueShare::create([
                    'cryptocurrency_id' => $cryptocurrencyId,
                    'transaction_id' => $transaction->id,
                    'revenue_amount' => $totalPrice,
                    'distribution_amount' => $creatorFee,
                    'is_distributed' => false
                ]);
            }
            
            return $transaction;
        } catch (\Exception $e) {
            Log::error('Error buying tokens: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Get market data for a cryptocurrency.
     *
     * @param int $cryptocurrencyId
     * @param string $period
     * @return array
     */
    public function getMarketData(int $cryptocurrencyId, string $period = 'week')
    {
        try {
            $cryptocurrency = Cryptocurrency::findOrFail($cryptocurrencyId);
            
            // Get transactions for the cryptocurrency
            $query = CryptoTransaction::where('cryptocurrency_id', $cryptocurrencyId)
                ->where('status', CryptoTransaction::COMPLETED_STATUS)
                ->whereIn('type', [CryptoTransaction::BUY_TYPE, CryptoTransaction::SELL_TYPE]);
            
            // Filter by period
            switch ($period) {
                case 'day':
                    $query->where('created_at', '>=', now()->subDay());
                    break;
                case 'week':
                    $query->where('created_at', '>=', now()->subWeek());
                    break;
                case 'month':
                    $query->where('created_at', '>=', now()->subMonth());
                    break;
                case 'year':
                    $query->where('created_at', '>=', now()->subYear());
                    break;
                default:
                    $query->where('created_at', '>=', now()->subWeek());
            }
            
            $transactions = $query->orderBy('created_at')->get();
            
            // Generate price chart data
            $chartData = [];
            $volumeData = [];
            $labels = [];
            
            if ($transactions->isEmpty()) {
                // If no transactions, use initial price
                $chartData[] = $cryptocurrency->initial_price;
                $volumeData[] = 0;
                $labels[] = now()->format('Y-m-d H:i');
            } else {
                foreach ($transactions as $transaction) {
                    $chartData[] = $transaction->price_per_token;
                    $volumeData[] = $transaction->amount;
                    $labels[] = $transaction->created_at->format('Y-m-d H:i');
                }
            }
            
            // Calculate market stats
            $marketCap = $cryptocurrency->current_price * $cryptocurrency->available_supply;
            $circulatingSupply = $cryptocurrency->available_supply;
            $totalSupply = $cryptocurrency->total_supply;
            $priceChange = $cryptocurrency->price_change_percentage;
            
            return [
                'chart_data' => $chartData,
                'volume_data' => $volumeData,
                'labels' => $labels,
                'market_cap' => $marketCap,
                'circulating_supply' => $circulatingSupply,
                'total_supply' => $totalSupply,
                'price_change' => $priceChange
            ];
        } catch (\Exception $e) {
            Log::error('Error getting market data: ' . $e->getMessage());
            return [
                'chart_data' => [],
                'volume_data' => [],
                'labels' => [],
                'market_cap' => 0,
                'circulating_supply' => 0,
                'total_supply' => 0,
                'price_change' => 0
            ];
        }
    }

    /**
     * Get trending cryptocurrencies.
     *
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTrendingCryptocurrencies(int $limit = 5)
    {
        return Cryptocurrency::where('is_active', true)
            ->orderByDesc('current_price')
            ->limit($limit)
            ->get();
    }
}