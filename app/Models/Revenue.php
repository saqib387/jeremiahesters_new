<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    protected $table = 'crypto_revenue_shares';

    protected $fillable = [
        'cryptocurrency_id',
        'user_id',
        'transaction_id',
        'percentage',
        'revenue_amount',
        'distribution_amount',
        'is_distributed',
        'distributed_at',
    ];

    protected $casts = [
        'percentage' => 'decimal:2',
        'revenue_amount' => 'decimal:8',
        'distribution_amount' => 'decimal:8',
        'is_distributed' => 'boolean',
        'distributed_at' => 'datetime',
    ];

    /**
     * Get the user that owns the revenue share
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
     * Get the cryptocurrency for this revenue share
     */
    public function cryptocurrency()
    {
        return $this->belongsTo('\App\Models\Cryptocurrency', 'cryptocurrency_id');
    }

    /**
     * Get the transaction associated with this revenue share
     */
    public function transaction()
    {
        // You might need to adjust this based on your transaction model
        return $this->belongsTo('\App\Models\Transaction', 'transaction_id');
    }

    /**
     * Scope for distributed revenue shares
     */
    public function scopeDistributed($query)
    {
        return $query->where('is_distributed', true);
    }

    /**
     * Scope for pending revenue shares
     */
    public function scopePending($query)
    {
        return $query->where('is_distributed', false);
    }

    /**
     * Scope for revenue shares by cryptocurrency
     */
    public function scopeByCryptocurrency($query, $cryptocurrencyId)
    {
        return $query->where('cryptocurrency_id', $cryptocurrencyId);
    }

    /**
     * Scope for revenue shares by user
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get formatted revenue amount
     */
    public function getFormattedRevenueAmountAttribute()
    {
        return number_format($this->revenue_amount, 8);
    }

    /**
     * Get formatted distribution amount
     */
    public function getFormattedDistributionAmountAttribute()
    {
        return number_format($this->distribution_amount, 8);
    }

    /**
     * Get formatted percentage
     */
    public function getFormattedPercentageAttribute()
    {
        return number_format($this->percentage, 2) . '%';
    }

    /**
     * Get distribution status badge class
     */
    public function getStatusBadgeClassAttribute()
    {
        return $this->is_distributed ? 'label-success' : 'label-warning';
    }

    /**
     * Get distribution status text
     */
    public function getStatusTextAttribute()
    {
        return $this->is_distributed ? 'Distributed' : 'Pending';
    }

    /**
     * Get revenue amount in USD (if cryptocurrency has current_price)
     */
    public function getRevenueAmountUsdAttribute()
    {
        if ($this->cryptocurrency && $this->cryptocurrency->current_price) {
            return $this->revenue_amount * $this->cryptocurrency->current_price;
        }
        return 0;
    }

    /**
     * Get distribution amount in USD
     */
    public function getDistributionAmountUsdAttribute()
    {
        if ($this->cryptocurrency && $this->cryptocurrency->current_price) {
            return $this->distribution_amount * $this->cryptocurrency->current_price;
        }
        return 0;
    }

    /**
     * Get formatted revenue amount in USD
     */
    public function getFormattedRevenueAmountUsdAttribute()
    {
        $usdValue = $this->revenue_amount_usd;
        return '$' . number_format($usdValue, 2);
    }

    /**
     * Get formatted distribution amount in USD
     */
    public function getFormattedDistributionAmountUsdAttribute()
    {
        $usdValue = $this->distribution_amount_usd;
        return '$' . number_format($usdValue, 2);
    }

    /**
     * Get time since creation
     */
    public function getTimeSinceCreatedAttribute()
    {
        return $this->created_at ? $this->created_at->diffForHumans() : 'Unknown';
    }

    /**
     * Get time since distribution
     */
    public function getTimeSinceDistributedAttribute()
    {
        return $this->distributed_at ? $this->distributed_at->diffForHumans() : 'Not distributed';
    }

    /**
     * Check if revenue share is overdue (created more than 30 days ago and not distributed)
     */
    public function getIsOverdueAttribute()
    {
        if ($this->is_distributed) {
            return false;
        }
        
        return $this->created_at && $this->created_at->diffInDays(now()) > 30;
    }

    /**
     * Get priority level based on amount and age
     */
    public function getPriorityLevelAttribute()
    {
        if ($this->is_distributed) {
            return 'completed';
        }

        $daysSinceCreated = $this->created_at ? $this->created_at->diffInDays(now()) : 0;
        $amountUsd = $this->revenue_amount_usd;

        if ($daysSinceCreated > 30 || $amountUsd > 1000) {
            return 'high';
        } elseif ($daysSinceCreated > 14 || $amountUsd > 100) {
            return 'medium';
        }

        return 'low';
    }

    /**
     * Get priority color class
     */
    public function getPriorityColorAttribute()
    {
        switch ($this->priority_level) {
            case 'high':
                return 'text-danger';
            case 'medium':
                return 'text-warning';
            case 'low':
                return 'text-info';
            case 'completed':
                return 'text-success';
            default:
                return 'text-muted';
        }
    }
}