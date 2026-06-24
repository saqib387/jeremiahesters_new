<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Model\User;
use App\Models\Video;
use App\Models\Stream;

class CreatorController extends Controller
{
    /**
     * Display the creator dashboard
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Get user's videos
        $videos = Video::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Get user's streams
        $streams = Stream::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();
        
        // Calculate stats with null-safe sums
        $totalViews = $videos->sum(fn($v) => $v->views_count ?? 0);
        $totalLikes = $videos->sum(fn($v) => $v->likes_count ?? 0);
        $totalComments = $videos->sum(fn($v) => $v->comments_count ?? 0);
        $totalVideos = $videos->count();
        $totalStreams = $streams->count();
        
        // Check verification status
        $verification = $user->verification;
        $isVerified = $verification && $verification->status === 'verified';
        $verificationStatus = $verification ? $verification->status : 'not_submitted';
        
        return view('creator.dashboard', compact(
            'user',
            'videos',
            'streams',
            'totalViews',
            'totalLikes',
            'totalComments',
            'totalVideos',
            'totalStreams',
            'isVerified',
            'verificationStatus'
        ));
    }
    
    /**
     * Display creator's videos management page
     */
    public function videos()
    {
        $user = Auth::user();
        $videos = Video::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
            
        return view('creator.videos', compact('videos'));
    }
    
    /**
     * Display creator's streams management page
     */
    public function streams()
    {
        $user = Auth::user();
        $streams = Stream::where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->paginate(12);
        
        // Check verification status for livestreaming
        $verification = $user->verification;
        $canStream = $verification && $verification->status === 'verified';
            
        return view('creator.streams', compact('streams', 'canStream'));
    }
    
    /**
     * Display creator's analytics page
     */
    public function analytics()
    {
        $user = Auth::user();
        
        // Get videos with stats - order by created_at as fallback if views_count doesn't exist
        try {
            $videos = Video::where('user_id', $user->id)
                ->orderByRaw('COALESCE(views_count, 0) DESC')
                ->get();
        } catch (\Exception $e) {
            $videos = Video::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
        }
        
        // Calculate overall stats with null-safe sums
        $stats = [
            'total_views' => $videos->sum(fn($v) => $v->views_count ?? 0),
            'total_likes' => $videos->sum(fn($v) => $v->likes_count ?? 0),
            'total_comments' => $videos->sum(fn($v) => $v->comments_count ?? 0),
            'total_shares' => $videos->sum(fn($v) => $v->shares_count ?? 0),
            'total_reposts' => $videos->sum(fn($v) => $v->reposts_count ?? 0),
            'total_videos' => $videos->count(),
            'avg_views' => $videos->count() > 0 ? round($videos->sum(fn($v) => $v->views_count ?? 0) / $videos->count()) : 0,
            'avg_likes' => $videos->count() > 0 ? round($videos->sum(fn($v) => $v->likes_count ?? 0) / $videos->count()) : 0,
        ];
        
        // Top performing videos
        $topVideos = $videos->take(5);
        
        return view('creator.analytics', compact('stats', 'topVideos', 'videos'));
    }
    
    /**
     * Display creator settings page
     */
    public function settings()
    {
        $user = Auth::user();
        $verification = $user->verification;
        
        return view('creator.settings', compact('user', 'verification'));
    }
}
