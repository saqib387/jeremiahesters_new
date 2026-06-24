<?php

namespace App\Models;

use App\Model\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class NFTListing extends Model
{
    use HasFactory;

    protected $table = 'nft_listings';

    protected $fillable = [
        'nft_id',
        'seller_id',
        'token_id',
        'price',
        'listing_price',
        'status',
        'listed_at',
        'sold_at',
        'transaction_hash',
    ];

    protected $casts = [
        'price' => 'decimal:8',
        'listing_price' => 'decimal:8',
        'listed_at' => 'datetime',
        'sold_at' => 'datetime',
    ];

    /**
     * Get the NFT for this listing
     */
    public function nft(): BelongsTo
    {
        return $this->belongsTo(NFT::class);
    }

    /**
     * Get the seller
     */
    public function seller(): BelongsTo
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    /**
     * Get transactions for this listing
     */
    public function transactions(): HasMany
    {
        // Explicit FK: basename "NFTListing" would otherwise infer "n_f_t_listing_id".
        return $this->hasMany(NFTTransaction::class, 'listing_id');
    }
}
