<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Config;

class StreamingServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Merge streaming config
        $this->mergeConfigFrom(
            __DIR__.'/../../config/streaming.php', 'streaming'
        );

        // Publish streaming config
        $this->publishes([
            __DIR__.'/../../config/streaming.php' => config_path('streaming.php'),
        ], 'streaming-config');

        // Ensure required directories exist
        $this->ensureDirectoriesExist();
    }

    /**
     * Ensure required directories exist for streaming
     */
    protected function ensureDirectoriesExist(): void
    {
        $directories = [
            storage_path('app/public/streams'),
            storage_path('app/public/streams/thumbnails'),
            public_path('hls'),
        ];

        foreach ($directories as $directory) {
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }
        }
    }
} 