<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use App\User;

class Stream extends Model
{
    use HasFactory;

    // Stream status constants
    const PENDING_STATUS = 'pending';
    const IN_PROGRESS_STATUS = 'live';
    const ENDED_STATUS = 'ended';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'title',
        'description',
        'slug',
        'stream_key',
        'thumbnail',
        'is_live',
        'started_at',
        'ended_at',
        'viewer_count',
        'peak_viewer_count',
        'requires_subscription',
        'is_public',
        'price',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_live' => 'boolean',
        'requires_subscription' => 'boolean',
        'is_public' => 'boolean',
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    /**
     * Get the user that owns the stream
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the chat messages for the stream
     */
    public function messages()
    {
        return $this->hasMany(ChatMessage::class);
    }

    /**
     * Get the stream's URL attributes.
     */
    public function getStreamUrlAttribute()
    {
        return route('streams.watch', $this);
    }

    /**
     * Get the stream's thumbnail URL.
     */
    public function getThumbnailUrlAttribute()
    {
        if ($this->thumbnail) {
            return asset('storage/' . $this->thumbnail);
        }
        
        return asset('img/default-stream-thumbnail.jpg');
    }

    /**
     * Generate a unique stream key for this stream
     */
    public function generateStreamKey()
    {
        $this->stream_key = Str::random(32);
        $this->save();
        return $this->stream_key;
    }

    /**
     * Start the livestream
     */
    public function start()
    {
        $this->is_live = true;
        $this->started_at = now();
        $this->status = 'live';
        $this->save();
    }

    /**
     * End the livestream
     */
    public function end()
    {
        $this->is_live = false;
        $this->ended_at = now();
        $this->status = 'ended';
        $this->save();
    }

    /**
     * Get the formatted duration.
     */
    public function getFormattedDurationAttribute()
    {
        if ($this->duration) {
            $hours = floor($this->duration / 60);
            $minutes = $this->duration % 60;
            
            if ($hours > 0) {
                return $hours . 'h ' . $minutes . 'm';
            }
            
            return $minutes . 'm';
        }
        
        if ($this->is_live && $this->started_at) {
            $minutes = now()->diffInMinutes($this->started_at);
            $hours = floor($minutes / 60);
            $mins = $minutes % 60;
            
            if ($hours > 0) {
                return $hours . 'h ' . $mins . 'm';
            }
            
            return $mins . 'm';
        }
        
        return '0m';
    }

    /**
     * Scope a query to only include live streams.
     */
    public function scopeLive($query)
    {
        return $query->where('is_live', true)
                    ->where('status', self::IN_PROGRESS_STATUS);
    }

    /**
     * Scope a query to only include public streams.
     */
    public function scopePublic($query)
    {
        return $query->where('is_public', true);
    }

    /**
     * Scope a query to only include streams by a specific user.
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to order streams by viewer count.
     */
    public function scopePopular($query)
    {
        return $query->orderBy('viewer_count', 'desc');
    }
} 