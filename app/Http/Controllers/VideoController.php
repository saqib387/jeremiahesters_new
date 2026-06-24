<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoCategory;
use App\Models\VideoComment;
use App\Models\VideoLike;
use App\Models\VideoShare;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;

class VideoController extends Controller
{
    /**
     * Display the reels-style video feed
     */
    public function reels()
    {
        try {
            $videos = Video::with('user')
                ->where('status', 'published')
                ->orderBy('created_at', 'desc')
                ->paginate(10);
                
            return view('videos.reels', compact('videos'));
            
        } catch (\Exception $e) {
            \Log::error('Reels error: ' . $e->getMessage());
            return "Error: " . $e->getMessage();
        }
    }

    /**
     * Display a listing of videos
     */
    public function index()
    {
        $query = Video::with('user')
            ->where('is_public', true)
            ->orderBy('created_at', 'desc');

        // Status: prefer 'published' or 'ready' if column exists
        if (\Schema::hasColumn((new Video)->getTable(), 'status')) {
            $query->whereIn('status', ['published', 'ready']);
        }
        // is_approved filter only if column exists
        if (\Schema::hasColumn((new Video)->getTable(), 'is_approved')) {
            $query->where('is_approved', true);
        }

        $videos = $query->paginate(12);

        return view('videos.index', compact('videos'));
    }

    /**
     * Show the form for creating a new video
     */
    public function create()
    {
        // Simple return - matching debug route exactly
        $postingWarnings = [];
        $canPost = true;
        
        // Try to get posting requirements if available
        try {
            if (class_exists('\App\Providers\GenericHelperServiceProvider')) {
                $postingCheck = \App\Providers\GenericHelperServiceProvider::canUserPost();
                $postingWarnings = $postingCheck['can_post'] ? [] : $postingCheck['errors'];
                $canPost = $postingCheck['can_post'];
            }
        } catch (\Exception $e) {
            // If check fails, just allow posting and log the error
            \Log::warning('Video create - canUserPost check failed: ' . $e->getMessage());
        }
        
        return view('videos.create', [
            'postingWarnings' => $postingWarnings,
            'canPost' => $canPost
        ]);
    }

    /**
     * Store a newly created video
     */


public function store(Request $request)
{
    try {
        // Check all posting requirements (18+, ID verification, bank account)
        $postingCheck = \App\Providers\GenericHelperServiceProvider::canUserPost();
        if (!$postingCheck['can_post']) {
            return back()->withErrors(['error' => implode(' ', $postingCheck['errors'])])->withInput();
        }

        // Validation
        $request->validate([
            'title' => 'required|string|max:191',
            'description' => 'nullable|string|max:1000',
            'video' => 'required|file|mimes:mp4,mov,webm,avi|max:20480', // 20MB
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120', // 5MB
        ]);

        $videoPath = null;
        $thumbnailPath = null;
        
        // Upload video file
        if ($request->hasFile('video')) {
            $video = $request->file('video');
            $extension = $video->getClientOriginalExtension();
            $fileName = time() . '_' . \Str::random(10) . '.' . $extension;
            $video->move(public_path('storage/videos'), $fileName);
            $videoPath = 'videos/' . $fileName;
        }
        
        // Upload thumbnail file
        if ($request->hasFile('thumbnail')) {
            $thumbnail = $request->file('thumbnail');
            $extension = $thumbnail->getClientOriginalExtension();
            $fileName = time() . '_thumb_' . \Str::random(8) . '.' . $extension;
            $thumbnail->move(public_path('storage/thumbnails'), $fileName);
            $thumbnailPath = 'thumbnails/' . $fileName;
        }
        
        // Create video record
        $video = \App\Models\Video::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'video_path' => $videoPath,
            'thumbnail_path' => $thumbnailPath,
            'is_public' => true,
            'is_private' => false,
            'status' => 'published',
        ]);

        return redirect()->route('videos.reels')
            ->with('success', 'Video uploaded successfully!');
        
    } catch (\Illuminate\Validation\ValidationException $e) {
        return back()->withErrors($e->errors())->withInput();
        
    } catch (\Exception $e) {
        // Clean up uploaded files if database save failed
        if (isset($videoPath) && file_exists(public_path('storage/' . $videoPath))) {
            unlink(public_path('storage/' . $videoPath));
        }
        if (isset($thumbnailPath) && file_exists(public_path('storage/' . $thumbnailPath))) {
            unlink(public_path('storage/' . $thumbnailPath));
        }
        
        return back()->withErrors(['error' => 'Upload failed: ' . $e->getMessage()])->withInput();
    }
}

    /**
     * Display the specified video
     */
    public function show(Video $video)
    {
        $video->load('user', 'comments.user');
        
        // Increment view count
        $video->increment('views_count');
        
        return view('videos.show', compact('video'));
    }

    /**
     * Show the form for editing the specified video
     */
    public function edit(Video $video)
    {
        // Check if user is authorized to edit
        if (Auth::id() !== $video->user_id && !Auth::user()->isAdmin()) {
            return redirect()->route('videos.index')
                ->with('error', 'You are not authorized to edit this video.');
        }
        
        return view('videos.edit', compact('video'));
    }

    /**
     * Update the specified video
     */
    public function update(Request $request, Video $video)
    {
        // Check if user is authorized to update
        if (Auth::id() !== $video->user_id && !Auth::user()->isAdmin()) {
            return redirect()->route('videos.index')
                ->with('error', 'You are not authorized to update this video.');
        }
        
        $request->validate([
            'title' => 'required|string|max:191',
            'description' => 'nullable|string|max:1000',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
            'tags' => 'nullable|string|max:255',
        ]);
        
        // Handle thumbnail update if provided
        if ($request->hasFile('thumbnail')) {
            // Delete old thumbnail
            if ($video->thumbnail_path) {
                Storage::disk('public')->delete($video->thumbnail_path);
            }
            
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails', 'public');
            $video->thumbnail_path = $thumbnailPath;
        }
        
        $video->title = $request->title;
        $video->description = $request->description;
        $video->tags = $request->tags;
        $video->save();
        
        return redirect()->route('videos.show', $video)
            ->with('success', 'Video updated successfully!');
    }

    /**
     * Remove the specified video
     */
    public function destroy(Video $video)
    {
        // Check if user is authorized to delete
        if (Auth::id() !== $video->user_id && !Auth::user()->isAdmin()) {
            return redirect()->route('videos.index')
                ->with('error', 'You are not authorized to delete this video.');
        }
        
        try {
            // Delete associated files
            if ($video->video_path) {
                Storage::disk('public')->delete($video->video_path);
            }
            
            if ($video->thumbnail_path) {
                Storage::disk('public')->delete($video->thumbnail_path);
            }
            
            $video->delete();
            
            return redirect()->route('videos.index')
                ->with('success', 'Video deleted successfully!');
                
        } catch (\Exception $e) {
            \Log::error('Video deletion error: ' . $e->getMessage());
            return back()->with('error', 'Failed to delete video: ' . $e->getMessage());
        }
    }
    
    /**
     * Like or unlike a video
     */
 public function toggleLike(Video $video)
{
    try {
        $user = Auth::user();
        
        $isLiked = $video->likes()->where('user_id', $user->id)->exists();
        
        if ($isLiked) {
            $video->likes()->detach($user->id);
            $video->decrement('likes_count');
            $action = 'unliked';
        } else {
            $video->likes()->attach($user->id);
            $video->increment('likes_count');
            $action = 'liked';
        }
        
        return response()->json([
            'success' => true,
            'action' => $action,
            'likes_count' => $video->fresh()->likes_count,
            'is_liked' => !$isLiked
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to toggle like'
        ], 500);
    }
}

    /**
     * Get video comments
     */
  public function comments(Video $video)
{
    try {
        $comments = $video->comments()
            ->with('user:id,name,username,avatar')
            ->orderBy('created_at', 'desc')
            ->paginate(20);
            
        return response()->json([
            'success' => true,
            'comments' => $comments->items(),
            'has_more' => $comments->hasMorePages(),
            'next_page' => $comments->nextPageUrl()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to load comments'
        ], 500);
    }
}

    /**
     * Add a comment to video
     */
   public function comment(Request $request, Video $video)
{
    try {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $comment = $video->comments()->create([
            'user_id' => Auth::id(),
            'content' => $request->content
        ]);

        // Increment comment count
        $video->increment('comments_count');

        $comment->load('user:id,name,username,avatar');

        return response()->json([
            'success' => true,
            'message' => 'Comment added successfully',
            'comment' => $comment
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to add comment'
        ], 500);
    }
}

    /**
     * Share a video
     */
   public function share(Request $request, Video $video)
{
    try {
        $request->validate([
            'platform' => 'required|string|in:facebook,twitter,whatsapp,telegram,web'
        ]);

        // Create share record if VideoShare model exists
        if (class_exists('App\Models\VideoShare')) {
            \App\Models\VideoShare::create([
                'video_id' => $video->id,
                'user_id' => Auth::id(),
                'platform' => $request->platform
            ]);
        }

        // Increment share count
        $video->increment('shares_count');

        return response()->json([
            'success' => true,
            'message' => 'Video shared successfully',
            'shares_count' => $video->shares_count
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'error' => 'Failed to share video'
        ], 500);
    }
}


    /**
     * Get user's videos
     */
    public function myVideos()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $videos = Video::where('user_id', Auth::id())
            ->with(['categories'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('videos.my-videos', compact('videos'));
    }
}