<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'ip',
        'agent',
        'device_id',
        'verified_at',
        'last_login',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'last_login' => 'datetime',
    ];

    /**
     * Get the user that owns this device.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
} 