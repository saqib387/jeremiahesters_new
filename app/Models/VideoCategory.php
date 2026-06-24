<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class VideoCategory extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description'
    ];

    public function videos(): BelongsToMany
    {
        return $this->belongsToMany(Video::class, 'video_category_relations');
    }
} 