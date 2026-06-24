<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cryptocurrency extends Model
{
    protected $table = 'cryptocurrencies';

    protected $fillable = [
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
        'creator_user_id',
        'creator_fee_percentage',
        'platform_fee_percentage',
        'liquidity_pool_percentage',
        'is_verified',
        'is_active',
    ];

    protected $casts = [
        'enable_burning' => 'boolean',
        'enable_minting' => 'boolean',
        'transferable' => 'boolean',
        'is_verified' => 'boolean',
        'is_active' => 'boolean',
        'price_history' => 'json',
    ];

    /**
     * Get the creator of the token
     */
    public function creator()
    {
        // Try different possible User model locations
        if (class_exists('\App\Models\User')) {
            return $this->belongsTo('\App\Models\User', 'creator_user_id');
        } elseif (class_exists('\TCG\Voyager\Models\User')) {
            return $this->belongsTo('\TCG\Voyager\Models\User', 'creator_user_id');
        } else {
            return $this->belongsTo('\App\User', 'creator_user_id');
        }
    }

    /**
     * Scope for verified tokens
     */
    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    /**
     * Scope for active tokens
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for tokens by type
     */
    public function scopeByType($query, $type)
    {
        return $query->where('token_type', $type);
    }

    /**
     * Scope for tokens by network
     */
    public function scopeByNetwork($query, $network)
    {
        return $query->where('blockchain_network', $network);
    }

    /**
     * Get formatted market cap
     */
    public function getFormattedMarketCapAttribute()
    {
        if ($this->market_cap >= 1000000000) {
            return '$' . number_format($this->market_cap / 1000000000, 2) . 'B';
        } elseif ($this->market_cap >= 1000000) {
            return '$' . number_format($this->market_cap / 1000000, 2) . 'M';
        } elseif ($this->market_cap >= 1000) {
            return '$' . number_format($this->market_cap / 1000, 2) . 'K';
        }
        return '$' . number_format($this->market_cap, 2);
    }

    /**
     * Get formatted volume
     */
    public function getFormattedVolumeAttribute()
    {
        if ($this->volume_24h >= 1000000000) {
            return '$' . number_format($this->volume_24h / 1000000000, 2) . 'B';
        } elseif ($this->volume_24h >= 1000000) {
            return '$' . number_format($this->volume_24h / 1000000, 2) . 'M';
        } elseif ($this->volume_24h >= 1000) {
            return '$' . number_format($this->volume_24h / 1000, 2) . 'K';
        }
        return '$' . number_format($this->volume_24h, 2);
    }

    /**
     * Get price change color class
     */
    public function getPriceChangeColorAttribute()
    {
        return $this->change_24h >= 0 ? 'text-success' : 'text-danger';
    }

    /**
     * Get price change icon
     */
    public function getPriceChangeIconAttribute()
    {
        return $this->change_24h >= 0 ? 'voyager-arrow-up' : 'voyager-arrow-down';
    }

    /**
     * Calculate total fees
     */
    public function getTotalFeesAttribute()
    {
        return $this->creator_fee_percentage + $this->platform_fee_percentage;
    }

    /**
     * Check if token is frozen (not transferable)
     */
    public function getIsFrozenAttribute()
    {
        return !$this->transferable;
    }
}