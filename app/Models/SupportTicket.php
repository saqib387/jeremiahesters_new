<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SupportTicket extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'custom_request_id',
        'type',
        'priority',
        'status',
        'subject',
        'description',
        'assigned_to',
        'resolution',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
    ];

    // Ticket types
    const TYPE_GENERAL = 'general';
    const TYPE_DISPUTE = 'dispute';
    const TYPE_PAYMENT = 'payment';
    const TYPE_VOTING = 'voting';
    const TYPE_TECHNICAL = 'technical';

    // Priorities
    const PRIORITY_LOW = 'low';
    const PRIORITY_NORMAL = 'normal';
    const PRIORITY_HIGH = 'high';
    const PRIORITY_URGENT = 'urgent';

    // Statuses
    const STATUS_OPEN = 'open';
    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_RESOLVED = 'resolved';
    const STATUS_CLOSED = 'closed';
    const STATUS_ESCALATED = 'escalated';

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ticket) {
            if (empty($ticket->ticket_number)) {
                $ticket->ticket_number = 'TKT-' . strtoupper(uniqid());
            }
        });
    }

    /**
     * Get the user who created the ticket
     */
    public function user()
    {
        return $this->belongsTo(\App\User::class, 'user_id');
    }

    /**
     * Get the custom request (if applicable)
     */
    public function customRequest()
    {
        return $this->belongsTo(CustomRequest::class);
    }

    /**
     * Get the assigned staff member
     */
    public function assignedTo()
    {
        return $this->belongsTo(\App\User::class, 'assigned_to');
    }

    /**
     * Get the user who resolved the ticket
     */
    public function resolvedBy()
    {
        return $this->belongsTo(\App\User::class, 'resolved_by');
    }

    /**
     * Get all messages for this ticket
     */
    public function messages()
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id');
    }

    /**
     * Get public messages (excluding internal notes)
     */
    public function publicMessages()
    {
        return $this->hasMany(SupportTicketMessage::class, 'ticket_id')
            ->where('is_internal', false);
    }

    /**
     * Check if ticket is open
     */
    public function isOpen()
    {
        return in_array($this->status, [self::STATUS_OPEN, self::STATUS_IN_PROGRESS]);
    }

    /**
     * Check if ticket is resolved
     */
    public function isResolved()
    {
        return in_array($this->status, [self::STATUS_RESOLVED, self::STATUS_CLOSED]);
    }
}
