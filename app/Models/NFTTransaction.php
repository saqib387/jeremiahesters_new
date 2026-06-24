<?php

namespace App\Models;

use App\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NFTTransaction extends Model
{
    use HasFactory;

    protected $table = 'nft_transactions';

    protected $fillable = [
        'nft_id',
        'listing_id',
        'seller_id',
        'buyer_id',
        'token_id',
        'chain_id',
        'contract_address',
        'type',
        'price',
        'fee',
        'transaction_hash',
        'from_address',
        'to_address',
        'block_number',
        'log_index',
        'confirmed_at',
        'status',
        'metadata',
    ];

    protected $casts = [
        'price' => 'decimal:8',
        'fee' => 'decimal:8',
        'chain_id' => 'integer',
        'block_number' => 'integer',
        'log_index' => 'integer',
        'confirmed_at' => 'datetime',
        'metadata' => 'array',
    ];

    /**
     * Get the NFT for this transaction
     */
    public function nft(): BelongsTo
    {
        return $this->belongsTo(NFT::class);
    }

    /**
     * Get the listing for this transaction
     */
    public function listing(): BelongsTo
    {
        return $this->belongsTo(NFTListing::class);
    }

    /**
     * Get the seller
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get the buyer
     */
    public function buyer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'buyer_id');
    }
}
