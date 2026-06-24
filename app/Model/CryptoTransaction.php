<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CryptoTransaction extends Model
{
    public const BUY_TYPE = 'buy';
    public const SELL_TYPE = 'sell';
    public const TRANSFER_TYPE = 'transfer';
    public const MINT_TYPE = 'mint';
    public const REWARD_TYPE = 'reward';
    
    public const PENDING_STATUS = 'pending';
    public const COMPLETED_STATUS = 'completed';
    public const FAILED_STATUS = 'failed';
    public const CANCELLED_STATUS = 'cancelled';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cryptocurrency_id', 'buyer_user_id', 'seller_user_id', 'type', 
        'amount', 'price_per_token', 'total_price', 'fee_amount',
        'transaction_hash', 'status'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price_per_token' => 'decimal:8',
        'total_price' => 'decimal:8',
        'fee_amount' => 'decimal:8',
    ];

    /**
     * Get the cryptocurrency associated with this transaction
     */
    public function cryptocurrency()
    {
        return $this->belongsTo('App\Model\Cryptocurrency');
    }

    /**
     * Get the buyer associated with this transaction
     */
    public function buyer()
    {
        return $this->belongsTo('App\User', 'buyer_user_id');
    }

    /**
     * Get the seller associated with this transaction
     */
    public function seller()
    {
        return $this->belongsTo('App\User', 'seller_user_id');
    }

    /**
     * Check if this transaction is a purchase
     */
    public function isBuy()
    {
        return $this->type === self::BUY_TYPE;
    }

    /**
     * Check if this transaction is a sale
     */
    public function isSell()
    {
        return $this->type === self::SELL_TYPE;
    }

    /**
     * Check if this transaction is completed
     */
    public function isCompleted()
    {
        return $this->status === self::COMPLETED_STATUS;
    }
} 