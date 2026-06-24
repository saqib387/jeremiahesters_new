<?php

// Replace your VideoCommentController with this version that doesn't increment comments_count

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class VideoCommentController extends Controller
{
    /**
     * Store a new comment (for regular form submissions)
     */
    public function store(Request $request, Video $video)
    {
        $request->validate([
            'content' => 'required|string|max:1000',
        ]);

        $comment = $video->comments()->create([
            'user_id' => auth()->id(),
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', 'Comment added successfully.');
    }

    /**
     * Store a new comment (for AJAX requests from reels)
     */
    public function storeAjax(Request $request, Video $video)
    {
        try {
            \Log::info('AJAX Comment request received', [
                'video_id' => $video->id,
                'user_id' => Auth::id(),
                'content' => $request->content
            ]);

            $request->validate([
                'content' => 'required|string|max:1000'
            ]);

            // Create the comment using direct database insertion
            $commentId = DB::table('video_comments')->insertGetId([
                'video_id' => $video->id,
                'user_id' => Auth::id(),
                'content' => $request->content,
                'created_at' => now(),
                'updated_at' => now()
            ]);

            \Log::info('Comment created successfully', ['comment_id' => $commentId]);

            // DON'T increment comment count for now - we'll add this after the migration
            // $video->increment('comments_count');

            // Get user data
            $user = Auth::user();

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => [
                    'id' => $commentId,
                    'content' => $request->content,
                    'created_at' => now()->toISOString(),
                    'user' => [
                        'id' => $user->id,
                        'name' => $user->name,
                        'username' => $user->username ?? $user->name,
                        'avatar' => $user->avatar ?? '/img/default-avatar.png'
                    ]
                ]
            ]);
            
        } catch (\Illuminate\Validation\ValidationException $e) {
            \Log::error('Comment validation failed', $e->errors());
            return response()->json([
                'success' => false,
                'error' => 'Validation failed',
                'details' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            \Log::error('Comment creation failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to add comment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Get comments for a video (AJAX)
     */
    public function index(Video $video)
    {
        try {
            \Log::info('Loading comments for video', ['video_id' => $video->id]);

            // Use direct database query to avoid relationship issues
            $comments = DB::table('video_comments')
                ->join('users', 'video_comments.user_id', '=', 'users.id')
                ->where('video_comments.video_id', $video->id)
                ->select(
                    'video_comments.id',
                    'video_comments.content',
                    'video_comments.created_at',
                    'users.id as user_id',
                    'users.name as user_name',
                    'users.username as user_username',
                    'users.avatar as user_avatar'
                )
                ->orderBy('video_comments.created_at', 'desc')
                ->limit(20)
                ->get();

            \Log::info('Comments loaded successfully', [
                'video_id' => $video->id,
                'comment_count' => $comments->count()
            ]);

            $formattedComments = $comments->map(function($comment) {
                return [
                    'id' => $comment->id,
                    'content' => $comment->content,
                    'created_at' => $comment->created_at,
                    'user' => [
                        'id' => $comment->user_id,
                        'name' => $comment->user_name,
                        'username' => $comment->user_username ?? $comment->user_name,
                        'avatar' => $comment->user_avatar ?? '/img/default-avatar.png'
                    ]
                ];
            });
                
            return response()->json([
                'success' => true,
                'comments' => $formattedComments,
                'has_more' => false,
                'next_page' => null
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Comments loading failed: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to load comments: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a comment
     */
    public function destroy(VideoComment $comment)
    {
        try {
            // Check if user can delete this comment
            if ($comment->user_id !== Auth::id()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized'
                ], 403);
            }

            $videoId = $comment->video_id;
            $comment->delete();

            // DON'T decrement comment count for now
            // $video->decrement('comments_count');

            return response()->json([
                'success' => true,
                'message' => 'Comment deleted successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('Comment deletion failed: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'error' => 'Failed to delete comment'
            ], 500);
        }
    }
}