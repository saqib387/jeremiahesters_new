<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;
use App\Model\User; // Fixed: was App\Model\User
use App\Models\Comment;

class Video extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
    'user_id',
    'title',
    'description',
    'video_path',
    'thumbnail_path',
    'is_public',
    'is_private',
    'status',
    // Remove these columns as they don't exist in your table:
    // 'tags', 'duration', 'views_count', 'likes_count', 'comments_count', 
    // 'shares_count', 'is_approved', 'is_featured', 'processed', 'processing_failed'
];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'duration' => 'float',
        'views_count' => 'integer',
        'likes_count' => 'integer',
        'comments_count' => 'integer',
        'shares_count' => 'integer',
        'is_approved' => 'boolean',
        'is_featured' => 'boolean',
        'processed' => 'boolean',
        'processing_failed' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Get the user that owns the video.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the comments for the video.
     */
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    /**
     * Get the users who liked this video.
     */
    public function likes(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'video_likes', 'video_id', 'user_id')->withTimestamps();
    }

    /**
     * Check if a user has liked this video.
     *
     * @param int $userId
     * @return bool
     */
    public function isLikedBy($userId): bool
    {
        return $this->likes()->where('user_id', $userId)->exists();
    }

    /**
     * Increment the view count.
     *
     * @return $this
     */
    public function incrementViewCount()
    {
        $this->increment('views_count');
        return $this;
    }

    /**
     * Get formatted duration.
     *
     * @return string
     */
    public function getFormattedDurationAttribute(): string
    {
        $minutes = floor($this->duration / 60);
        $seconds = $this->duration % 60;
        
        return sprintf('%02d:%02d', $minutes, $seconds);
    }

    /**
     * Get the video's status label.
     *
     * @return string
     */
    public function getStatusLabelAttribute(): string
    {
        switch ($this->status) {
            case 'published':
                return 'Published';
            case 'processing':
                return 'Processing';
            case 'draft':
                return 'Draft';
            case 'rejected':
                return 'Rejected';
            default:
                return 'Unknown';
        }
    }

    /**
     * Scope a query to only include published videos.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published')
                    ->where('is_approved', true)
                    ->where('processed', true);
    }

    /**
     * Scope a query to only include featured videos.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include videos by a specific user.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to order videos by popularity.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopePopular($query)
    {
        return $query->orderBy('views_count', 'desc')
                    ->orderBy('likes_count', 'desc');
    }

    /**
     * Scope a query to filter videos by tag.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $tag
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeWithTag($query, $tag)
    {
        return $query->where('tags', 'like', '%' . $tag . '%');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(VideoCategory::class, 'video_category_relations');
    }

    public function shares(): HasMany
    {
        return $this->hasMany(VideoShare::class);
    }

    /**
     * Get the video URL attribute - generates URL from stored path
     */
    public function getVideoUrlAttribute(): string
    {
        if ($this->video_path) {
            return Storage::url($this->video_path);
        }
        return '';
    }

    /**
     * Get the thumbnail URL attribute - generates URL from stored path
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        if ($this->thumbnail_path) {
            return Storage::url($this->thumbnail_path);
        }
        return null;
    }

    /**
     * Get video display URL
     */
    public function getVideoDisplayUrlAttribute(): string
    {
        return $this->getVideoUrlAttribute();
    }

    /**
     * Get thumbnail display URL
     */
    public function getThumbnailDisplayUrlAttribute(): ?string
    {
        return $this->getThumbnailUrlAttribute();
    }

}