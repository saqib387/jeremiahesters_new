<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class SetupStreaming extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'streaming:setup';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set up the streaming environment';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Setting up streaming environment...');

        // Create required directories
        $directories = [
            storage_path('app/public/streams'),
            storage_path('app/public/streams/thumbnails'),
            public_path('hls'),
        ];

        foreach ($directories as $directory) {
            if (!File::exists($directory)) {
                File::makeDirectory($directory, 0755, true);
                $this->info("Created directory: {$directory}");
            }
        }

        // Create symbolic link for public storage
        if (!File::exists(public_path('storage'))) {
            $this->call('storage:link');
        }

        // Check for required software
        $this->checkRequiredSoftware();

        $this->info('Streaming environment setup complete!');
        $this->info('Please make sure to:');
        $this->info('1. Install and configure an RTMP server (e.g., nginx-rtmp)');
        $this->info('2. Update your .env file with the correct RTMP and HLS URLs');
        $this->info('3. Configure your streaming software (e.g., OBS Studio) with the RTMP URL and stream key');
    }

    /**
     * Check for required software
     */
    protected function checkRequiredSoftware()
    {
        $this->info('Checking for required software...');

        // Check for FFmpeg
        exec('ffmpeg -version', $output, $returnCode);
        if ($returnCode === 0) {
            $this->info('✓ FFmpeg is installed');
        } else {
            $this->warn('✗ FFmpeg is not installed. Please install FFmpeg for thumbnail generation.');
        }

        // Check for nginx-rtmp
        exec('nginx -v 2>&1', $output, $returnCode);
        if ($returnCode === 0) {
            $this->info('✓ Nginx is installed');
            // Check if nginx-rtmp module is installed
            exec('nginx -V 2>&1', $output, $returnCode);
            if (strpos(implode(' ', $output), 'rtmp') !== false) {
                $this->info('✓ nginx-rtmp module is installed');
            } else {
                $this->warn('✗ nginx-rtmp module is not installed. Please install the nginx-rtmp module.');
            }
        } else {
            $this->warn('✗ Nginx is not installed. Please install Nginx with the rtmp module.');
        }
    }
} 