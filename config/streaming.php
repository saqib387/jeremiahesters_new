<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Streaming Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for the streaming system.
    |
    */

    // RTMP server configuration
    'rtmp_server' => env('RTMP_SERVER', 'rtmp://127.0.0.1/live'),

    // HLS server configuration
    'hls_server' => env('HLS_SERVER', 'http://127.0.0.1:8080/hls'),

    // Stream quality presets
    'quality_presets' => [
        'low' => [
            'width' => 640,
            'height' => 360,
            'bitrate' => '800k',
            'audio_bitrate' => '96k'
        ],
        'medium' => [
            'width' => 854,
            'height' => 480,
            'bitrate' => '1400k',
            'audio_bitrate' => '128k'
        ],
        'high' => [
            'width' => 1280,
            'height' => 720,
            'bitrate' => '2800k',
            'audio_bitrate' => '192k'
        ]
    ],

    // Default stream settings
    'default_quality' => 'medium',
    'max_duration' => 7200, // 2 hours in seconds
    'thumbnail_interval' => 10, // Generate thumbnail every 10 seconds
]; 