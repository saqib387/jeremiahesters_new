<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CryptoWallet extends Model
{
    use HasFactory;

    protected $table = 'crypto_wallets';

    protected $fillable = [
        'user_id',
        'cryptocurrency_id',
        'balance',
        'wallet_address',
        'is_active'
    ];

    protected $casts = [
        'balance' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the wallet.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the cryptocurrency for this wallet.
     */
    public function cryptocurrency(): BelongsTo
    {
        return $this->belongsTo(Cryptocurrency::class);
    }

    /**
     * Get the wallet balance in USD.
     */
    public function getBalanceUsdAttribute(): float
    {
        return $this->balance * $this->cryptocurrency->current_price;
    }

    /**
     * Get formatted wallet address (truncated).
     */
    public function getFormattedAddressAttribute(): string
    {
        if (!$this->wallet_address) {
            return 'N/A';
        }
        
        return substr($this->wallet_address, 0, 6) . '...' . substr($this->wallet_address, -4);
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($wallet) {
            if (is_null($wallet->is_active)) {
                $wallet->is_active = true;
            }
            if (is_null($wallet->balance)) {
                $wallet->balance = 0;
            }
        });
    }
}