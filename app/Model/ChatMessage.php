<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use TCG\Voyager\Models\User;

class ChatMessage extends Model
{
    protected $table = 'stream_chat_messages';
    
    protected $fillable = [
        'stream_id',
        'user_id',
        'message',
        'is_system'
    ];

    protected $casts = [
        'is_system' => 'boolean'
    ];

    public function stream()
    {
        return $this->belongsTo(Stream::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 