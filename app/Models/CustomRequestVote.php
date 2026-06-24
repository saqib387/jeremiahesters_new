<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CustomRequestVote extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'custom_request_id',
        'voter_id',
        'vote_type',
        'comment',
        'is_requester',
        'is_contributor',
        'contribution_amount',
    ];

    protected $casts = [
        'is_requester' => 'boolean',
        'is_contributor' => 'boolean',
        'contribution_amount' => 'decimal:2',
    ];

    // Vote types
    const VOTE_APPROVE = 'approve';
    const VOTE_REJECT = 'reject';
    const VOTE_ABSTAIN = 'abstain';

    /**
     * Get the custom request
     */
    public function customRequest()
    {
        return $this->belongsTo(CustomRequest::class);
    }

    /**
     * Get the voter (user who voted)
     */
    public function voter()
    {
        return $this->belongsTo(\App\User::class, 'voter_id');
    }

    /**
     * Check if vote is approval
     */
    public function isApproval()
    {
        return $this->vote_type === self::VOTE_APPROVE;
    }

    /**
     * Check if vote is rejection
     */
    public function isRejection()
    {
        return $this->vote_type === self::VOTE_REJECT;
    }
}
