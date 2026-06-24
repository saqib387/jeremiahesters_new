<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class Cryptocurrency extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'creator_user_id', 'name', 'symbol', 'logo', 'description', 
        'initial_price', 'current_price', 'total_supply', 'available_supply', 
        'blockchain_network', 'contract_address', 'contract_abi',
        'is_verified', 'is_active', 'creator_fee_percentage', 'platform_fee_percentage'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'initial_price' => 'decimal:8',
        'current_price' => 'decimal:8',
        'creator_fee_percentage' => 'decimal:2',
        'platform_fee_percentage' => 'decimal:2',
        'is_verified' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Get the creator of this cryptocurrency
     */
    public function creator()
    {
        return $this->belongsTo('App\User', 'creator_user_id');
    }

    /**
     * Get all transactions for this cryptocurrency
     */
    public function transactions()
    {
        return $this->hasMany('App\Model\CryptoTransaction');
    }

    /**
     * Get all wallet holdings for this cryptocurrency
     */
    public function wallets()
    {
        return $this->hasMany('App\Model\CryptoWallet');
    }

    /**
     * Get revenue shares for this cryptocurrency
     */
    public function revenueShares()
    {
        return $this->hasMany('App\Model\CryptoRevenueShare');
    }

    /**
     * Calculate market cap
     */
    public function getMarketCapAttribute()
    {
        return $this->current_price * $this->available_supply;
    }

    /**
     * Calculate price change percentage
     */
    public function getPriceChangePercentageAttribute()
    {
        if ($this->initial_price == 0) {
            return 0;
        }
        
        return (($this->current_price - $this->initial_price) / $this->initial_price) * 100;
    }
} 