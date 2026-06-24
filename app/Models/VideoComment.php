<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VideoComment extends Model
{
    use HasFactory;

    protected $table = 'video_comments';

    protected $fillable = [
        'video_id',
        'user_id',
        'content',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function video(): BelongsTo
    {
        return $this->belongsTo(Video::class);
    }

    public function user(): BelongsTo
    {
        // Try both User model locations
        if (class_exists('App\Models\User')) {
            return $this->belongsTo(\App\Models\User::class);
        } else {
            return $this->belongsTo(\App\User::class);
        }
    }
}