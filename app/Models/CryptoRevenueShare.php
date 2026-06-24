<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CryptoRevenueShare extends Model
{
    use HasFactory;

    protected $table = 'crypto_revenue_shares';

    protected $fillable = [
        'cryptocurrency_id',
        'user_id',
        'transaction_id',
        'amount',
        'share_type',
        'status',
        'distributed_at'
    ];

    protected $casts = [
        'amount' => 'decimal:8',
        'distributed_at' => 'datetime',
    ];

    const SHARE_TYPE_CREATOR = 'creator';
    const SHARE_TYPE_PLATFORM = 'platform';
    const SHARE_TYPE_REFERRAL = 'referral';

    const STATUS_PENDING = 'pending';
    const STATUS_DISTRIBUTED = 'distributed';
    const STATUS_FAILED = 'failed';

    /**
     * Get the cryptocurrency for this revenue share.
     */
    public function cryptocurrency(): BelongsTo
    {
        return $this->belongsTo(Cryptocurrency::class);
    }

    /**
     * Get the user who receives this revenue share.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    /**
     * Get the transaction that generated this revenue share.
     */
    public function transaction(): BelongsTo
    {
        return $this->belongsTo(CryptoTransaction::class);
    }

    /**
     * Get the share type badge color.
     */
    public function getShareTypeBadgeColorAttribute(): string
    {
        switch ($this->share_type) {
            case self::SHARE_TYPE_CREATOR:
                return 'bg-blue-100 text-blue-800';
            case self::SHARE_TYPE_PLATFORM:
                return 'bg-purple-100 text-purple-800';
            case self::SHARE_TYPE_REFERRAL:
                return 'bg-green-100 text-green-800';
            default:
                return 'bg-gray-100 text-gray-800';
        }
    }

    /**
     * Boot method for model events
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($revenueShare) {
            if (is_null($revenueShare->status)) {
                $revenueShare->status = self::STATUS_PENDING;
            }
        });
    }
}