<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomRequestContribution extends Model
{
    use HasFactory;

    protected $fillable = [
        'custom_request_id',
        'contributor_id',
        'amount',
        'transaction_id',
        'status',
        'message',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
    ];

    // Statuses
    const STATUS_PENDING = 'pending';
    const STATUS_COMPLETED = 'completed';
    const STATUS_REFUNDED = 'refunded';

    /**
     * Get the custom request
     */
    public function customRequest()
    {
        return $this->belongsTo(CustomRequest::class);
    }

    /**
     * Get the contributor (user who contributed)
     */
    public function contributor()
    {
        return $this->belongsTo(\App\User::class, 'contributor_id');
    }

    /**
     * Get the transaction
     */
    public function transaction()
    {
        return $this->belongsTo(\App\Model\Transaction::class);
    }
}
