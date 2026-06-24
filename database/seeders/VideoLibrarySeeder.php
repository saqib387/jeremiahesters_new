<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use App\Models\Video;
use App\Model\User;

class VideoLibrarySeeder extends Seeder
{
    /**
     * Seed videos from storage/app/public/videos into the database.
     *
     * @return void
     */
    public function run()
    {
        $disk = Storage::disk('public');

        if (!$disk->exists('videos')) {
            $this->command?->warn('No videos folder found at storage/app/public/videos.');
            return;
        }

        $files = collect($disk->files('videos'))
            ->filter(function ($path) {
                return preg_match('/\.(mp4|mov|webm|avi)$/i', $path);
            })
            ->values();

        if ($files->isEmpty()) {
            $this->command?->warn('No video files found in storage/app/public/videos.');
            return;
        }

        $user = User::query()->orderBy('id')->first();
        if (!$user) {
            $user = User::create([
                'name' => 'Seeder User',
                'email' => 'seed_' . Str::lower(Str::random(8)) . '@example.com',
                'username' => 'seeduser_' . Str::lower(Str::random(6)),
                'password' => Hash::make('password'),
            ]);
        }

        $columns = Schema::getColumnListing('videos');

        foreach ($files as $path) {
            $filename = pathinfo($path, PATHINFO_FILENAME);
            $title = trim(Str::title(str_replace(['-', '_'], ' ', $filename)));
            $createdAt = now();

            try {
                $createdAt = \Carbon\Carbon::createFromTimestamp($disk->lastModified($path));
            } catch (\Exception $e) {
                // Fallback to now()
            }

            $data = [
                'user_id' => $user->id,
                'title' => $title !== '' ? $title : 'Seeded Video',
                'description' => 'Seeded from storage/app/public/videos',
                'video_path' => $path,
                'thumbnail_path' => null,
                'is_public' => true,
                'is_private' => false,
                'status' => 'published',
                'views_count' => 0,
                'likes_count' => 0,
                'comments_count' => 0,
                'shares_count' => 0,
                'reposts_count' => 0,
                'created_at' => $createdAt,
                'updated_at' => $createdAt,
            ];

            $data = array_filter(
                $data,
                function ($value, $key) use ($columns) {
                    return in_array($key, $columns, true);
                },
                ARRAY_FILTER_USE_BOTH
            );

            Video::firstOrCreate(['video_path' => $path], $data);
        }

        $this->command?->info('VideoLibrarySeeder: seeded ' . $files->count() . ' video(s).');
    }
}
