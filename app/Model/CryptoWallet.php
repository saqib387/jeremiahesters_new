<?php

namespace App\Model;

use App\User;
use Illuminate\Database\Eloquent\Model;

class CryptoWallet extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'cryptocurrency_id', 'balance', 'wallet_address', 'private_key_encrypted'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'private_key_encrypted',
    ];

    /**
     * Get the user associated with this wallet
     */
    public function user()
    {
        return $this->belongsTo('App\User');
    }

    /**
     * Get the cryptocurrency associated with this wallet
     */
    public function cryptocurrency()
    {
        return $this->belongsTo('App\Model\Cryptocurrency');
    }

    /**
     * Calculate the wallet value in USD
     */
    public function getValueAttribute()
    {
        $crypto = $this->cryptocurrency;
        if ($crypto) {
            return $crypto->current_price * $this->balance;
        }
        return 0;
    }

    /**
     * Check if wallet has sufficient balance for a transaction
     */
    public function hasSufficientBalance($amount)
    {
        return $this->balance >= $amount;
    }
} 