<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\User;

class ChatMessage extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'stream_id',
        'user_id',
        'message',
        'is_pinned',
        'is_highlighted',
        'is_deleted',
        'deleted_by'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_pinned' => 'boolean',
        'is_highlighted' => 'boolean',
        'is_deleted' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the stream that the message belongs to.
     */
    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    /**
     * Get the user that owns the message
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the user who deleted the message.
     */
    public function deletedBy()
    {
        return $this->belongsTo(User::class, 'deleted_by');
    }

    /**
     * Scope a query to only include non-deleted messages.
     */
    public function scopeVisible($query)
    {
        return $query->where('is_deleted', false);
    }

    /**
     * Scope a query to only include pinned messages.
     */
    public function scopePinned($query)
    {
        return $query->where('is_pinned', true);
    }

    /**
     * Scope a query to only include highlighted messages.
     */
    public function scopeHighlighted($query)
    {
        return $query->where('is_highlighted', true);
    }

    /**
     * Mark the message as deleted.
     */
    public function markAsDeleted($deletedByUserId)
    {
        $this->is_deleted = true;
        $this->deleted_by = $deletedByUserId;
        $this->save();
        
        return $this;
    }

    /**
     * Pin the message.
     */
    public function pin()
    {
        $this->is_pinned = true;
        $this->save();
        
        return $this;
    }

    /**
     * Unpin the message.
     */
    public function unpin()
    {
        $this->is_pinned = false;
        $this->save();
        
        return $this;
    }

    /**
     * Highlight the message.
     */
    public function highlight()
    {
        $this->is_highlighted = true;
        $this->save();
        
        return $this;
    }

    /**
     * Remove highlight from the message.
     */
    public function unhighlight()
    {
        $this->is_highlighted = false;
        $this->save();
        
        return $this;
    }
} 