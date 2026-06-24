<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;

class Cryptocurrency extends Model
{
    use HasFactory;

    protected $table = 'cryptocurrencies';

    protected $fillable = [
        'creator_user_id',
        'name',
        'symbol',
        'logo',
        'description',
        'website',
        'whitepaper',
        'initial_price',
        'current_price',
        'market_cap',
        'total_supply',
        'available_supply',
        'circulating_supply',
        'max_supply',
        'volume_24h',
        'change_24h',
        'price_history',
        'blockchain_network',
        'token_type',
        'enable_burning',
        'enable_minting',
        'transferable',
        'contract_address',
        'contract_abi',
        'creator_fee_percentage',
        'platform_fee_percentage',
        'liquidity_pool_percentage',
        'is_verified',
        'is_active'
    ];

    protected $casts = [
        'initial_price' => 'decimal:8',
        'current_price' => 'decimal:8',
        'market_cap' => 'decimal:2',
        'total_supply' => 'decimal:8',      // Changed to 8 decimals for better precision
        'available_supply' => 'decimal:8',   // Changed to 8 decimals
        'circulating_supply' => 'decimal:8', // Changed to 8 decimals
        'max_supply' => 'decimal:8',         // Changed to 8 decimals
        'volume_24h' => 'decimal:2',
        'change_24h' => 'decimal:4',
        'creator_fee_percentage' => 'decimal:2',
        'platform_fee_percentage' => 'decimal:2',
        'liquidity_pool_percentage' => 'decimal:2',
        'enable_burning' => 'boolean',
        'enable_minting' => 'boolean',
        'transferable' => 'boolean',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'price_history' => 'json',
    ];

    protected $appends = [
        'logo_url',
        'price_change_percentage',
        'formatted_market_cap',
        'formatted_volume',
        'total_holders'
    ];

    /**
     * Get the creator of the cryptocurrency.
     */
    public function creator(): BelongsTo
    {
        // Try different possible User model locations
        if (class_exists('\App\Models\User')) {
            return $this->belongsTo('\App\Models\User', 'creator_user_id');
        }
        
        if (class_exists('\App\User')) {
            return $this->belongsTo('\App\User', 'creator_user_id');
        }
        
        // Fallback to Laravel's default auth user model
        return $this->belongsTo(config('auth.providers.users.model'), 'creator_user_id');
    }

    /**
     * Get the transactions for the cryptocurrency.
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(CryptoTransaction::class);
    }

    /**
     * Get the wallets for the cryptocurrency.
     */
    public function wallets(): HasMany
    {
        return $this->hasMany(CryptoWallet::class);
    }

    /**
     * Get buy transactions only.
     */
    public function buyTransactions(): HasMany
    {
        return $this->hasMany(CryptoTransaction::class)->where('type', 'buy');
    }

    /**
     * Get sell transactions only.
     */
    public function sellTransactions(): HasMany
    {
        return $this->hasMany(CryptoTransaction::class)->where('type', 'sell');
    }

    /**
     * Get recent transactions (last 24 hours).
     */
    public function recentTransactions(): HasMany
    {
        return $this->hasMany(CryptoTransaction::class)
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc');
    }

    /**
     * Get the revenue shares for the cryptocurrency.
     */
    public function revenueShares(): HasMany
    {
        return $this->hasMany(CryptoRevenueShare::class);
    }

    /**
     * Get the logo URL - FIXED VERSION
     */
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo) {
            return null; // Return null instead of default image to use fallback in views
        }

        // Check if logo already contains full URL
        if (str_starts_with($this->logo, 'http')) {
            return $this->logo;
        }

        // Handle storage path - FIXED: Proper Storage disk checking
        if (Storage::disk('public')->exists($this->logo)) {
            return asset('storage/' . $this->logo);
        }

        // Handle legacy storage paths
        if (str_starts_with($this->logo, 'storage/')) {
            return asset($this->logo);
        }

        // Check if file exists in public storage
        if (Storage::disk('public')->exists('crypto-logos/' . $this->logo)) {
            return asset('storage/crypto-logos/' . $this->logo);
        }

        return null; // Return null if file doesn't exist
    }

    /**
     * Calculate price change percentage - FIXED VERSION
     */
    public function getPriceChangePercentageAttribute(): float
    {
        // Use change_24h if available, otherwise calculate from initial price
        if ($this->change_24h !== null) {
            return (float) $this->change_24h;
        }

        if ($this->initial_price == 0) {
            return 0;
        }

        return (($this->current_price - $this->initial_price) / $this->initial_price) * 100;
    }

    /**
     * Get formatted market cap
     */
    public function getFormattedMarketCapAttribute(): string
    {
        return $this->formatLargeNumber($this->market_cap);
    }

    /**
     * Get formatted volume
     */
    public function getFormattedVolumeAttribute(): string
    {
        return $this->formatLargeNumber($this->volume_24h);
    }

    /**
     * Get total holders count
     */
    public function getTotalHoldersAttribute(): int
    {
        return $this->wallets()->where('balance', '>', 0)->count();
    }

    /**
     * Get formatted price change with color
     */
    public function getPriceChangeColorAttribute(): string
    {
        $change = $this->price_change_percentage;
        if ($change > 0) return 'text-success';
        if ($change < 0) return 'text-danger';
        return 'text-muted';
    }

    /**
     * Get price change symbol
     */
    public function getPriceChangeSymbolAttribute(): string
    {
        $change = $this->price_change_percentage;
        if ($change > 0) return '+';
        return '';
    }

    /**
     * Get price change icon
     */
    public function getPriceChangeIconAttribute(): string
    {
        $change = $this->price_change_percentage;
        if ($change > 0) return 'fas fa-arrow-up';
        if ($change < 0) return 'fas fa-arrow-down';
        return 'fas fa-minus';
    }

    /**
     * Format large numbers (K, M, B)
     */
    private function formatLargeNumber($number): string
    {
        if ($number >= 1000000000) {
            return '$' . number_format($number / 1000000000, 2) . 'B';
        } elseif ($number >= 1000000) {
            return '$' . number_format($number / 1000000, 2) . 'M';
        } elseif ($number >= 1000) {
            return '$' . number_format($number / 1000, 2) . 'K';
        }
        return '$' . number_format($number, 2);
    }

    /**
     * Check if user owns this token
     */
    public function isOwnedByUser(int $userId): bool
    {
        return $this->wallets()
            ->where('user_id', $userId)
            ->where('balance', '>', 0)
            ->exists();
    }

    /**
     * Get user balance for this token
     */
    public function getUserBalance(int $userId): float
    {
        $wallet = $this->wallets()
            ->where('user_id', $userId)
            ->first();
        
        return $wallet ? (float) $wallet->balance : 0;
    }

    /**
     * Update market cap based on current price and circulating supply
     */
    public function updateMarketCap(): void
    {
        $this->market_cap = $this->circulating_supply * $this->current_price;
        $this->save();
    }

    /**
     * Update price and add to history
     */
    public function updatePrice(float $newPrice): void
    {
        $oldPrice = $this->current_price;
        $this->current_price = $newPrice;
        
        // Calculate 24h change percentage
        if ($oldPrice > 0) {
            $this->change_24h = (($newPrice - $oldPrice) / $oldPrice) * 100;
        }
        
        // Update market cap
        $this->market_cap = $this->circulating_supply * $newPrice;
        
        // Add to price history
        $priceHistory = json_decode($this->price_history ?? '[]', true);
        $priceHistory[] = [
            'price' => $newPrice,
            'timestamp' => now()->timestamp,
            'date' => now()->toISOString()
        ];
        
        // Keep only last 100 price points
        if (count($priceHistory) > 100) {
            $priceHistory = array_slice($priceHistory, -100);
        }
        
        $this->price_history = json_encode($priceHistory);
        $this->save();
    }

    /**
     * Scopes
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeTrending($query)
    {
        return $query->where('is_active', true)
            ->orderBy('volume_24h', 'desc');
    }

    public function scopeByNetwork($query, string $network)
    {
        $map = [
            'ETH' => ['eth', 'ethereum'],
            'BSC' => ['bsc', 'binance', 'binance_smart_chain', 'bnb'],
            'MATIC' => ['matic', 'polygon'],
            'ARB' => ['arb', 'arbitrum'],
        ];
        $needle = strtoupper(trim($network));
        $variants = $map[$needle] ?? [strtolower($network)];

        return $query->where(function ($q) use ($variants) {
            foreach ($variants as $v) {
                $q->orWhereRaw('LOWER(blockchain_network) = ?', [strtolower($v)]);
            }
        });
    }

    /**
     * Sum of fee percentages for admin display.
     */
    public function getTotalFeesAttribute(): float
    {
        return (float) $this->creator_fee_percentage
            + (float) $this->platform_fee_percentage
            + (float) $this->liquidity_pool_percentage;
    }

    /**
     * Normalized network badge label (ETH vs ethereum, etc.).
     */
    public function getDisplayNetworkLabelAttribute(): string
    {
        $labels = [
            'eth' => 'ETH',
            'ethereum' => 'ETH',
            'bsc' => 'BSC',
            'binance' => 'BSC',
            'binance_smart_chain' => 'BSC',
            'bnb' => 'BSC',
            'matic' => 'MATIC',
            'polygon' => 'MATIC',
            'arb' => 'ARB',
            'arbitrum' => 'ARB',
        ];
        $key = strtolower((string) $this->blockchain_network);

        return $labels[$key] ?? strtoupper($this->blockchain_network);
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('token_type', $type);
    }

    /**
     * Boot method for model events - UPDATED
     */
    protected static function boot()
    {
        parent::boot();

        // Set default values when creating
        static::creating(function ($cryptocurrency) {
            // Set current price to initial price if not set
            if (is_null($cryptocurrency->current_price)) {
                $cryptocurrency->current_price = $cryptocurrency->initial_price;
            }
            
            // Calculate initial market cap
            if (is_null($cryptocurrency->market_cap)) {
                $cryptocurrency->market_cap = ($cryptocurrency->circulating_supply ?? 0) * $cryptocurrency->current_price;
            }
            
            // Set default boolean values
            if (is_null($cryptocurrency->is_active)) {
                $cryptocurrency->is_active = true;
            }
            if (is_null($cryptocurrency->is_verified)) {
                $cryptocurrency->is_verified = false;
            }
            if (is_null($cryptocurrency->transferable)) {
                $cryptocurrency->transferable = true;
            }
            if (is_null($cryptocurrency->enable_burning)) {
                $cryptocurrency->enable_burning = false;
            }
            if (is_null($cryptocurrency->enable_minting)) {
                $cryptocurrency->enable_minting = false;
            }
            
            // Set default string values
            if (is_null($cryptocurrency->token_type)) {
                $cryptocurrency->token_type = 'utility';
            }
            if (is_null($cryptocurrency->blockchain_network)) {
                $cryptocurrency->blockchain_network = 'ethereum';
            }
            
            // Set default percentage values
            if (is_null($cryptocurrency->creator_fee_percentage)) {
                $cryptocurrency->creator_fee_percentage = 5.00;
            }
            if (is_null($cryptocurrency->platform_fee_percentage)) {
                $cryptocurrency->platform_fee_percentage = 2.50;
            }
            if (is_null($cryptocurrency->liquidity_pool_percentage)) {
                $cryptocurrency->liquidity_pool_percentage = 20.00;
            }
            
            // Set default numeric values
            if (is_null($cryptocurrency->volume_24h)) {
                $cryptocurrency->volume_24h = 0;
            }
            if (is_null($cryptocurrency->change_24h)) {
                $cryptocurrency->change_24h = 0;
            }
            
            // Set supply defaults
            if (is_null($cryptocurrency->available_supply)) {
                $cryptocurrency->available_supply = $cryptocurrency->total_supply;
            }
            if (is_null($cryptocurrency->circulating_supply)) {
                $cryptocurrency->circulating_supply = 0;
            }
            if (is_null($cryptocurrency->max_supply)) {
                $cryptocurrency->max_supply = $cryptocurrency->total_supply;
            }
            
            // Initialize price history
            if (is_null($cryptocurrency->price_history)) {
                $cryptocurrency->price_history = json_encode([
                    [
                        'price' => $cryptocurrency->initial_price,
                        'timestamp' => now()->timestamp,
                        'date' => now()->toISOString()
                    ]
                ]);
            }
        });

        // Update market cap when price changes
        static::updating(function ($cryptocurrency) {
            if ($cryptocurrency->isDirty('current_price') || $cryptocurrency->isDirty('circulating_supply')) {
                $cryptocurrency->market_cap = $cryptocurrency->circulating_supply * $cryptocurrency->current_price;
            }
        });
    }
}