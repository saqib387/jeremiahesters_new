<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class Feed extends Model
{
    // This model handles all feed-related operations without a specific table
    protected $table = null;
    public $timestamps = false;

    /**
     * Get all videos for feed with counts and user interactions
     */
    public static function getAllVideos($userId = null)
    {
        $hasLikes = Schema::hasTable('video_likes');
        $hasComments = Schema::hasTable('video_comments');
        $hasShares = Schema::hasTable('video_shares');
        $hasReposts = Schema::hasTable('video_reposts');

        $query = DB::table('videos as v')
            ->leftJoin('users as u', 'v.user_id', '=', 'u.id')
            ;

        if ($hasLikes) {
            $query->leftJoin('video_likes as vl', function ($join) use ($userId) {
                $join->on('v.id', '=', 'vl.video_id');
                if ($userId) {
                    $join->where('vl.user_id', '=', $userId);
                }
            });
        }

        if ($hasShares) {
            $query->leftJoin('video_shares as vs', function ($join) use ($userId) {
                $join->on('v.id', '=', 'vs.video_id');
                if ($userId) {
                    $join->where('vs.user_id', '=', $userId);
                }
            });
        }

        if ($hasReposts) {
            $query->leftJoin('video_reposts as vr', function ($join) use ($userId) {
                $join->on('v.id', '=', 'vr.video_id');
                if ($userId) {
                    $join->where('vr.user_id', '=', $userId);
                }
            });
        }

        return $query
            ->select([
                'v.id',
                'v.user_id',
                'v.title',
                'v.description',
                'v.video_path',
                'v.views_count',
                'v.created_at',
                'u.name as user_name',
                'u.username as user_username',
                // Get counts using subqueries for better performance
                $hasLikes ? DB::raw('(SELECT COUNT(*) FROM video_likes WHERE video_id = v.id) as likes_count') : DB::raw('0 as likes_count'),
                $hasComments ? DB::raw('(SELECT COUNT(*) FROM video_comments WHERE video_id = v.id) as comments_count') : DB::raw('0 as comments_count'),
                $hasShares ? DB::raw('(SELECT COUNT(*) FROM video_shares WHERE video_id = v.id) as shares_count') : DB::raw('0 as shares_count'),
                $hasReposts ? DB::raw('(SELECT COUNT(*) FROM video_reposts WHERE video_id = v.id) as reposts_count') : DB::raw('0 as reposts_count'),
                // Check if current user has interacted
                ($userId && $hasLikes) ? DB::raw('CASE WHEN vl.id IS NOT NULL THEN 1 ELSE 0 END as is_liked') : DB::raw('0 as is_liked'),
                ($userId && $hasShares) ? DB::raw('CASE WHEN vs.id IS NOT NULL THEN 1 ELSE 0 END as is_shared') : DB::raw('0 as is_shared'),
                ($userId && $hasReposts) ? DB::raw('CASE WHEN vr.id IS NOT NULL THEN 1 ELSE 0 END as is_reposted') : DB::raw('0 as is_reposted')
            ])
            ->where(function ($query) {
                $query->where('v.is_public', 1)
                    ->orWhereNull('v.is_public');
            })
            ->where(function ($query) {
                $query->whereIn('v.status', ['published', 'ready'])
                    ->orWhereNull('v.status');
            })
            ->orderBy('v.created_at', 'desc')
            ->get()
            ->map(function($video) {
                $video->video_url = asset('storage/' . $video->video_path);
                $video->user_initials = strtoupper(substr($video->user_name, 0, 1)) . 
                                       strtoupper(substr(explode(' ', $video->user_name)[1] ?? '', 0, 1));
                $video->username = $video->user_username ?? strtolower(str_replace(' ', '', $video->user_name));
                return $video;
            });
    }

    /**
     * Get videos by specific user
     */
    public static function getUserVideos($targetUserId, $currentUserId = null)
    {
        $hasLikes = Schema::hasTable('video_likes');
        $hasComments = Schema::hasTable('video_comments');
        $hasShares = Schema::hasTable('video_shares');
        $hasReposts = Schema::hasTable('video_reposts');

        $query = DB::table('videos as v')
            ->leftJoin('users as u', 'v.user_id', '=', 'u.id')
            ;

        if ($hasLikes) {
            $query->leftJoin('video_likes as vl', function ($join) use ($currentUserId) {
                $join->on('v.id', '=', 'vl.video_id');
                if ($currentUserId) {
                    $join->where('vl.user_id', '=', $currentUserId);
                }
            });
        }

        if ($hasShares) {
            $query->leftJoin('video_shares as vs', function ($join) use ($currentUserId) {
                $join->on('v.id', '=', 'vs.video_id');
                if ($currentUserId) {
                    $join->where('vs.user_id', '=', $currentUserId);
                }
            });
        }

        if ($hasReposts) {
            $query->leftJoin('video_reposts as vr', function ($join) use ($currentUserId) {
                $join->on('v.id', '=', 'vr.video_id');
                if ($currentUserId) {
                    $join->where('vr.user_id', '=', $currentUserId);
                }
            });
        }

        return $query
            ->select([
                'v.id', 'v.user_id', 'v.title', 'v.description', 'v.video_path', 'v.views_count', 'v.created_at',
                'u.name as user_name', 'u.username as user_username',
                $hasLikes ? DB::raw('(SELECT COUNT(*) FROM video_likes WHERE video_id = v.id) as likes_count') : DB::raw('0 as likes_count'),
                $hasComments ? DB::raw('(SELECT COUNT(*) FROM video_comments WHERE video_id = v.id) as comments_count') : DB::raw('0 as comments_count'),
                $hasShares ? DB::raw('(SELECT COUNT(*) FROM video_shares WHERE video_id = v.id) as shares_count') : DB::raw('0 as shares_count'),
                $hasReposts ? DB::raw('(SELECT COUNT(*) FROM video_reposts WHERE video_id = v.id) as reposts_count') : DB::raw('0 as reposts_count'),
                ($currentUserId && $hasLikes) ? DB::raw('CASE WHEN vl.id IS NOT NULL THEN 1 ELSE 0 END as is_liked') : DB::raw('0 as is_liked'),
                ($currentUserId && $hasShares) ? DB::raw('CASE WHEN vs.id IS NOT NULL THEN 1 ELSE 0 END as is_shared') : DB::raw('0 as is_shared'),
                ($currentUserId && $hasReposts) ? DB::raw('CASE WHEN vr.id IS NOT NULL THEN 1 ELSE 0 END as is_reposted') : DB::raw('0 as is_reposted')
            ])
            ->where('v.user_id', $targetUserId)
            ->where(function ($query) {
                $query->where('v.is_public', 1)
                    ->orWhereNull('v.is_public');
            })
            ->where(function ($query) {
                $query->whereIn('v.status', ['published', 'ready'])
                    ->orWhereNull('v.status');
            })
            ->orderBy('v.created_at', 'desc')
            ->get()
            ->map(function($video) {
                $video->video_url = asset('storage/' . $video->video_path);
                $video->user_initials = strtoupper(substr($video->user_name, 0, 1)) . 
                                       strtoupper(substr(explode(' ', $video->user_name)[1] ?? '', 0, 1));
                $video->username = $video->user_username ?? strtolower(str_replace(' ', '', $video->user_name));
                return $video;
            });
    }

    /**
     * Get video statistics
     */
    public static function getVideoStats($videoId, $userId = null)
    {
        $hasLikes = Schema::hasTable('video_likes');
        $hasComments = Schema::hasTable('video_comments');
        $hasShares = Schema::hasTable('video_shares');
        $hasReposts = Schema::hasTable('video_reposts');

        $query = DB::table('videos as v');

        if ($hasLikes) {
            $query->leftJoin('video_likes as vl', function ($join) use ($userId) {
                $join->on('v.id', '=', 'vl.video_id');
                if ($userId) {
                    $join->where('vl.user_id', '=', $userId);
                }
            });
        }

        if ($hasShares) {
            $query->leftJoin('video_shares as vs', function ($join) use ($userId) {
                $join->on('v.id', '=', 'vs.video_id');
                if ($userId) {
                    $join->where('vs.user_id', '=', $userId);
                }
            });
        }

        if ($hasReposts) {
            $query->leftJoin('video_reposts as vr', function ($join) use ($userId) {
                $join->on('v.id', '=', 'vr.video_id');
                if ($userId) {
                    $join->where('vr.user_id', '=', $userId);
                }
            });
        }

        $stats = $query
            ->where('v.id', $videoId)
            ->select([
                'v.views_count',
                $hasLikes ? DB::raw('(SELECT COUNT(*) FROM video_likes WHERE video_id = v.id) as likes_count') : DB::raw('0 as likes_count'),
                $hasComments ? DB::raw('(SELECT COUNT(*) FROM video_comments WHERE video_id = v.id) as comments_count') : DB::raw('0 as comments_count'),
                $hasShares ? DB::raw('(SELECT COUNT(*) FROM video_shares WHERE video_id = v.id) as shares_count') : DB::raw('0 as shares_count'),
                $hasReposts ? DB::raw('(SELECT COUNT(*) FROM video_reposts WHERE video_id = v.id) as reposts_count') : DB::raw('0 as reposts_count'),
                ($userId && $hasLikes) ? DB::raw('CASE WHEN vl.id IS NOT NULL THEN 1 ELSE 0 END as is_liked') : DB::raw('0 as is_liked'),
                ($userId && $hasShares) ? DB::raw('CASE WHEN vs.id IS NOT NULL THEN 1 ELSE 0 END as is_shared') : DB::raw('0 as is_shared'),
                ($userId && $hasReposts) ? DB::raw('CASE WHEN vr.id IS NOT NULL THEN 1 ELSE 0 END as is_reposted') : DB::raw('0 as is_reposted')
            ])
            ->first();

        return $stats;
    }

    /**
     * Toggle like on video
     */
    public static function toggleLike($videoId, $userId)
    {
        $existingLike = DB::table('video_likes')
            ->where('video_id', $videoId)
            ->where('user_id', $userId)
            ->first();

        if ($existingLike) {
            DB::table('video_likes')
                ->where('video_id', $videoId)
                ->where('user_id', $userId)
                ->delete();
            $action = 'unliked';
        } else {
            DB::table('video_likes')->insert([
                'video_id' => $videoId,
                'user_id' => $userId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
            $action = 'liked';
        }

        $likesCount = DB::table('video_likes')->where('video_id', $videoId)->count();

        return ['action' => $action, 'likes_count' => $likesCount];
    }

    /**
     * Add comment to video
     */
    public static function addComment($videoId, $userId, $content)
    {
        $commentId = DB::table('video_comments')->insertGetId([
            'video_id' => $videoId,
            'user_id' => $userId,
            'content' => $content,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $comment = DB::table('video_comments as vc')
            ->join('users as u', 'vc.user_id', '=', 'u.id')
            ->where('vc.id', $commentId)
            ->select([
                'vc.id', 'vc.content', 'vc.created_at',
                'u.name as user_name', 'u.username as user_username'
            ])
            ->first();

        $commentsCount = DB::table('video_comments')->where('video_id', $videoId)->count();

        return [
            'comment' => [
                'id' => $comment->id,
                'content' => $comment->content,
                'user' => [
                    'name' => $comment->user_name,
                    'username' => $comment->user_username ?? strtolower(str_replace(' ', '', $comment->user_name)),
                    'initials' => strtoupper(substr($comment->user_name, 0, 1)) . 
                                 strtoupper(substr(explode(' ', $comment->user_name)[1] ?? '', 0, 1))
                ],
                'created_at' => \Carbon\Carbon::parse($comment->created_at)->diffForHumans()
            ],
            'comments_count' => $commentsCount
        ];
    }

    /**
     * Get comments for video
     */
    public static function getComments($videoId, $page = 1, $limit = 20)
    {
        $offset = ($page - 1) * $limit;

        $comments = DB::table('video_comments as vc')
            ->join('users as u', 'vc.user_id', '=', 'u.id')
            ->where('vc.video_id', $videoId)
            ->select([
                'vc.id', 'vc.content', 'vc.created_at',
                'u.id as user_id', 'u.name as user_name', 'u.username as user_username'
            ])
            ->orderBy('vc.created_at', 'desc')
            ->limit($limit)
            ->offset($offset)
            ->get()
            ->map(function($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'user' => [
                        'id' => $comment->user_id,
                        'name' => $comment->user_name,
                        'username' => $comment->user_username ?? strtolower(str_replace(' ', '', $comment->user_name)),
                        'initials' => strtoupper(substr($comment->user_name, 0, 1)) . 
                                     strtoupper(substr(explode(' ', $comment->user_name)[1] ?? '', 0, 1))
                    ],
                    'created_at' => \Carbon\Carbon::parse($comment->created_at)->diffForHumans()
                ];
            });

        $totalComments = DB::table('video_comments')->where('video_id', $videoId)->count();

        return [
            'comments' => $comments,
            'total' => $totalComments,
            'has_more' => ($offset + $limit) < $totalComments
        ];
    }

    /**
     * Share video
     */
    public static function shareVideo($videoId, $userId, $platform = 'web')
    {
        DB::table('video_shares')->insert([
            'video_id' => $videoId,
            'user_id' => $userId,
            'platform' => $platform,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        $sharesCount = DB::table('video_shares')->where('video_id', $videoId)->count();

        return ['shares_count' => $sharesCount];
    }

    /**
     * Toggle repost on video
     */
    public static function toggleRepost($videoId, $userId)
    {
        $existingRepost = DB::table('video_reposts')
            ->where('video_id', $videoId)
            ->where('user_id', $userId)
            ->first();

        if ($existingRepost) {
            DB::table('video_reposts')
                ->where('video_id', $videoId)
                ->where('user_id', $userId)
                ->delete();
            $action = 'unreposted';
        } else {
            DB::table('video_reposts')->insert([
                'video_id' => $videoId,
                'user_id' => $userId,
                'reposted_at' => now()
            ]);
            $action = 'reposted';
        }

        $repostsCount = DB::table('video_reposts')->where('video_id', $videoId)->count();

        return ['action' => $action, 'reposts_count' => $repostsCount];
    }

    /**
     * Increment video views
     */
    public static function incrementViews($videoId)
    {
        DB::table('videos')
            ->where('id', $videoId)
            ->increment('views_count');

        $viewsCount = DB::table('videos')->where('id', $videoId)->value('views_count');

        return ['views_count' => $viewsCount];
    }

    /**
     * Search videos
     */
    public static function searchVideos($query, $limit = 20)
    {
        return DB::table('videos as v')
            ->join('users as u', 'v.user_id', '=', 'u.id')
            ->where('v.is_public', 1)
            ->whereIn('v.status', ['published', 'ready'])
            ->where(function($q) use ($query) {
                $q->where('v.title', 'like', "%{$query}%")
                  ->orWhere('v.description', 'like', "%{$query}%")
                  ->orWhere('u.name', 'like', "%{$query}%");
            })
            ->select([
                'v.id', 'v.title', 'v.description', 'v.views_count',
                'u.name as user_name', 'u.username as user_username',
                DB::raw('(SELECT COUNT(*) FROM video_likes WHERE video_id = v.id) as likes_count'),
                DB::raw('(SELECT COUNT(*) FROM video_comments WHERE video_id = v.id) as comments_count')
            ])
            ->orderBy('v.created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function($video) {
                $video->username = $video->user_username ?? strtolower(str_replace(' ', '', $video->user_name));
                return $video;
            });
    }

    /**
     * Get trending videos
     */
    public static function getTrendingVideos($limit = 50)
    {
        return DB::table('videos as v')
            ->join('users as u', 'v.user_id', '=', 'u.id')
            ->where('v.is_public', 1)
            ->whereIn('v.status', ['published', 'ready'])
            ->select([
                'v.*', 'u.name as user_name', 'u.username as user_username',
                DB::raw('(SELECT COUNT(*) FROM video_likes WHERE video_id = v.id) as likes_count'),
                DB::raw('(SELECT COUNT(*) FROM video_comments WHERE video_id = v.id) as comments_count'),
                DB::raw('(SELECT COUNT(*) FROM video_shares WHERE video_id = v.id) as shares_count'),
                DB::raw('(v.views_count * 0.4 + (SELECT COUNT(*) FROM video_likes WHERE video_id = v.id) * 0.3 + (SELECT COUNT(*) FROM video_comments WHERE video_id = v.id) * 0.2 + (SELECT COUNT(*) FROM video_shares WHERE video_id = v.id) * 0.1) as trend_score')
            ])
            ->orderByDesc('trend_score')
            ->orderByDesc('v.created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get user by username or name
     */
    public static function getUserByUsername($username)
    {
        return DB::table('users')
            ->where('name', $username)
            ->orWhere('username', $username)
            ->select(['id', 'name', 'username', 'email'])
            ->first();
    }
}