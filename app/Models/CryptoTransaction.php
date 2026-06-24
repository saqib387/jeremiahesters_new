<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CryptoTransaction extends Model
{
    use HasFactory;

    protected $table = 'crypto_transactions';

    // FIXED: Match actual database columns
    protected $fillable = [
        'wallet_id',
        'cryptocurrency_id',
        'buyer_user_id',
        'seller_user_id',
        'type',
        'transaction_type',
        'amount',
        'price_per_unit',        // DB column name
        'price_per_token',       // DB column name
        'total_price',
        'fee_amount',            // DB column name (not platform_fee/creator_fee)
        'from_address',
        'to_address',
        'related_user_id',
        'transaction_hash',
        'status',
        'notes'                  // DB column name (not metadata)
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'price_per_unit' => 'decimal:8',
        'price_per_token' => 'decimal:8',
        'total_price' => 'decimal:8',
        'fee_amount' => 'decimal:8',
        'notes' => 'json',  // Cast notes as JSON instead of metadata
    ];

    const TYPE_BUY = 'buy';
    const TYPE_SELL = 'sell';
    const TYPE_TRANSFER = 'transfer';
    const TYPE_MINT = 'mint';
    const TYPE_DEPOSIT = 'deposit';
    const TYPE_WITHDRAW = 'withdraw';
    const TYPE_CREATE = 'create';
    const TYPE_REWARD = 'reward';
    const TYPE_AIRDROP = 'airdrop';

    const BUY_TYPE = self::TYPE_BUY;
    const SELL_TYPE = self::TYPE_SELL;
    const TRANSFER_TYPE = self::TYPE_TRANSFER;
    const MINT_TYPE = self::TYPE_MINT;
    const DEPOSIT_TYPE = self::TYPE_DEPOSIT;
    const WITHDRAW_TYPE = self::TYPE_WITHDRAW;
    const CREATE_TYPE = self::TYPE_CREATE;
    const REWARD_TYPE = self::TYPE_REWARD;
    const AIRDROP_TYPE = self::TYPE_AIRDROP;

    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_FAILED = 'failed';
    const STATUS_CANCELLED = 'cancelled';

    const PENDING_STATUS = self::STATUS_PENDING;
    const COMPLETED_STATUS = self::STATUS_COMPLETED;
    const FAILED_STATUS = self::STATUS_FAILED;
    const CANCELLED_STATUS = self::STATUS_CANCELLED;

    public function getTransactionTypeAttribute()
    {
        return $this->attributes['type'] ?? null;
    }

    public function setTransactionTypeAttribute($value): void
    {
        $this->attributes['type'] = $value;
    }

    /**
     * Get the cryptocurrency for this transaction.
     */
    public function cryptocurrency(): BelongsTo
    {
        return $this->belongsTo(Cryptocurrency::class);
    }

    /**
     * Get the buyer user.
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'buyer_user_id');
    }

    /**
     * Get the seller user.
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'seller_user_id');
    }

    /**
     * Get the wallet associated with this transaction.
     */
    public function wallet(): BelongsTo
    {
        return $this->belongsTo(CryptoWallet::class, 'wallet_id');
    }

    /**
     * Get the related user.
     */
    public function relatedUser(): BelongsTo
    {
        return $this->belongsTo(config('auth.providers.users.model'), 'related_user_id');
    }

    /**
     * Get the transaction type badge color.
     */
    public function getTypeBadgeColorAttribute(): string
    {
        switch ($this->type) {
            case self::TYPE_BUY:
                return 'bg-success';
            case self::TYPE_SELL:
                return 'bg-danger';
            case self::TYPE_TRANSFER:
                return 'bg-primary';
            case self::TYPE_DEPOSIT:
                return 'bg-info';
            case self::TYPE_WITHDRAW:
                return 'bg-warning';
            case self::TYPE_CREATE:
                return 'bg-secondary';
            case self::TYPE_REWARD:
                return 'bg-success';
            case self::TYPE_AIRDROP:
                return 'bg-info';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Get the status badge color.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        switch ($this->status) {
            case self::STATUS_COMPLETED:
                return 'bg-success';
            case self::STATUS_PENDING:
                return 'bg-warning';
            case self::STATUS_FAILED:
                return 'bg-danger';
            case self::STATUS_CANCELLED:
                return 'bg-secondary';
            default:
                return 'bg-secondary';
        }
    }

    /**
     * Get formatted transaction hash.
     */
    public function getFormattedHashAttribute(): string
    {
        if (!$this->transaction_hash) {
            return 'N/A';
        }
        
        return substr($this->transaction_hash, 0, 10) . '...';
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (is_null($transaction->status)) {
                $transaction->status = self::STATUS_COMPLETED; // Default to completed as per DB
            }
            
            // Generate transaction hash if not provided (max 64 chars as per DB)
            if (is_null($transaction->transaction_hash)) {
                $transaction->transaction_hash = $transaction->type . '_' . time() . '_' . substr(md5(uniqid()), 0, 20);
            }
        });
    }
}
