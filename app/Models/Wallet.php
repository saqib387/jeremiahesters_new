<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $table = 'crypto_wallets';

    protected $fillable = [
        'user_id',
        'cryptocurrency_id',
        'balance',
        'wallet_address',
        'is_active',
    ];

    protected $casts = [
        'balance' => 'decimal:8',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the wallet
     */
    public function user()
    {
        // Try different possible User model locations
        if (class_exists('\App\Models\User')) {
            return $this->belongsTo('\App\Models\User', 'user_id');
        } elseif (class_exists('\TCG\Voyager\Models\User')) {
            return $this->belongsTo('\TCG\Voyager\Models\User', 'user_id');
        } else {
            return $this->belongsTo('\App\User', 'user_id');
        }
    }

    /**
     * Get the cryptocurrency for this wallet
     */
    public function cryptocurrency()
    {
        return $this->belongsTo('\App\Models\Cryptocurrency', 'cryptocurrency_id');
    }

    /**
     * Scope for active wallets
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for inactive wallets
     */
    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    /**
     * Scope for wallets with balance
     */
    public function scopeWithBalance($query)
    {
        return $query->where('balance', '>', 0);
    }

    /**
     * Scope for empty wallets
     */
    public function scopeEmpty($query)
    {
        return $query->where('balance', '=', 0);
    }

    /**
     * Get formatted balance
     */
    public function getFormattedBalanceAttribute()
    {
        return number_format($this->balance, 8);
    }

    /**
     * Get balance in USD (if cryptocurrency has current_price)
     */
    public function getBalanceUsdAttribute()
    {
        if ($this->cryptocurrency && $this->cryptocurrency->current_price) {
            return $this->balance * $this->cryptocurrency->current_price;
        }
        return 0;
    }

    /**
     * Get formatted balance in USD
     */
    public function getFormattedBalanceUsdAttribute()
    {
        $usdValue = (float) $this->balance_usd;

        return '$' . number_format($usdValue, 2);
    }

    /**
     * Check if wallet has address
     */
    public function getHasAddressAttribute()
    {
        return !empty($this->wallet_address);
    }

    /**
     * Get masked wallet address
     */
    public function getMaskedAddressAttribute()
    {
        if (!$this->wallet_address) {
            return 'No Address';
        }
        
        $address = $this->wallet_address;
        if (strlen($address) > 10) {
            return substr($address, 0, 6) . '...' . substr($address, -4);
        }
        
        return $address;
    }

    /**
     * Always false: the platform is non-custodial and no longer stores private keys.
     */
    public function getHasPrivateKeyAttribute()
    {
        return false;
    }

    /**
     * Get wallet status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return $this->is_active ? 'label-success' : 'label-danger';
    }

    /**
     * Get wallet status text
     */
    public function getStatusTextAttribute()
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }
}