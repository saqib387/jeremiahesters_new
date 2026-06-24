<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class CryptoRevenueShare extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cryptocurrency_id', 'transaction_id', 'revenue_amount', 
        'distribution_amount', 'is_distributed', 'distributed_at'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'revenue_amount' => 'decimal:8',
        'distribution_amount' => 'decimal:8',
        'is_distributed' => 'boolean',
        'distributed_at' => 'datetime',
    ];

    /**
     * Get the cryptocurrency associated with this revenue share
     */
    public function cryptocurrency()
    {
        return $this->belongsTo('App\Model\Cryptocurrency');
    }

    /**
     * Get the transaction associated with this revenue share
     */
    public function transaction()
    {
        return $this->belongsTo('App\Model\Transaction');
    }

    /**
     * Distribute this revenue share to token holders
     */
    public function distribute()
    {
        if ($this->is_distributed) {
            return false;
        }

        // Implementation would handle calculating each token holder's share
        // and creating the appropriate transaction records

        $this->is_distributed = true;
        $this->distributed_at = now();
        $this->save();

        return true;
    }
} 