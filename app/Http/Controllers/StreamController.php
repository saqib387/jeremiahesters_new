<?php

namespace App\Http\Controllers;

use App\Models\Stream;
use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StreamController extends Controller
{
    /**
     * Display a listing of streams
     */
    public function index()
    {
        $streams = Stream::with('user')
            ->where('is_live', true)
            ->orderBy('started_at', 'desc')
            ->paginate(12);

        return view('streams.index', compact('streams'));
    }

    /**
     * Show the form for creating a new stream
     */
    public function create()
    {
        // Check if user has verified their identity
        $user = Auth::user();
        $verification = $user->verification;
        
        if (!$verification || $verification->status !== 'verified') {
            return redirect()->route('my.settings', ['type' => 'verify'])
                ->with('error', 'You need to verify your identity before you can start a livestream. Please complete the ID verification process.');
        }
        
        return view('streams.create');
    }

    /**
     * Store a newly created stream
     */
    public function store(Request $request)
    {
        // Check if user has verified their identity
        $user = Auth::user();
        $verification = $user->verification;
        
        if (!$verification || $verification->status !== 'verified') {
            return redirect()->route('my.settings', ['type' => 'verify'])
                ->with('error', 'You need to verify your identity before you can start a livestream.');
        }
        
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'requires_subscription' => 'nullable|boolean',
            'is_public' => 'nullable',
            'price' => 'nullable|numeric|min:0',
        ]);

        $stream = new Stream();
        $stream->user_id = Auth::id();
        $stream->title = $request->title;
        $stream->description = $request->description;
        $stream->slug = Str::slug($request->title) . '-' . Str::random(5);
        $stream->requires_subscription = $request->has('requires_subscription');
        $stream->is_public = $request->has('is_public');
        $stream->price = $request->price ?? 0;
        $stream->status = 'pending';
        $stream->is_live = false;

        // Handle thumbnail upload
        if ($request->hasFile('thumbnail')) {
            $thumbnailPath = $request->file('thumbnail')->store('thumbnails/streams', 'public');
            $stream->thumbnail = $thumbnailPath;
        }

        $stream->save();
        
        // Generate stream key
        $stream->generateStreamKey();

        return redirect()->route('streams.broadcast', $stream)
            ->with('success', 'Stream created successfully. Use the stream key to start broadcasting.');
    }

    /**
     * Display the specified stream
     */
    public function show(Stream $stream)
    {
        $stream->load('user', 'messages.user');
        return view('streams.show', compact('stream'));
    }

    /**
     * Show the broadcaster view for a stream
     */
    public function broadcast(Stream $stream)
    {
        // Check if user is authorized to broadcast
        if ($stream->user_id !== Auth::id()) {
            abort(403, 'You are not authorized to broadcast this stream.');
        }

        return view('streams.broadcast', compact('stream'));
    }

    /**
     * Show the viewer view for a stream
     */
    public function watch(Stream $stream)
    {
        // Check if stream is live
        if (!$stream->is_live) {
            return redirect()->route('streams.index')
                ->with('error', 'This stream is not live.');
        }

        // Check if user has access to this stream
        if ($stream->requires_subscription && !$this->hasActiveSubscription(Auth::id(), $stream->user_id)) {
            return redirect()->route('streams.index')
                ->with('error', 'This stream requires a subscription.');
        }

        if ($stream->price > 0 && !$this->hasPaidForStream(Auth::id(), $stream->id)) {
            return redirect()->route('streams.index')
                ->with('error', 'This stream requires payment.');
        }

        $stream->load('user', 'messages.user');
        
        // Increment viewer count
        $stream->increment('viewer_count');
        
        return view('streams.watch', compact('stream'));
    }

    /**
     * End a stream
     */
    public function end(Request $request, Stream $stream)
    {
        // Check if user is authorized to end the stream
        if ($stream->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403, 'You are not authorized to end this stream.');
        }

        $stream->end();

        return redirect()->route('streams.index')
            ->with('success', 'Stream ended successfully.');
    }

    /**
     * Add a chat message to a stream
     */
    public function addMessage(Request $request, Stream $stream)
    {
        $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $message = new ChatMessage();
        $message->stream_id = $stream->id;
        $message->user_id = Auth::id();
        $message->message = $request->message;
        $message->save();

        return response()->json([
            'success' => true,
            'message' => $message->load('user'),
        ]);
    }

    /**
     * Check if a user has an active subscription to a creator
     */
    private function hasActiveSubscription($userId, $creatorId)
    {
        // This is a placeholder - implement your subscription check logic
        return true;
    }

    /**
     * Check if a user has paid for a stream
     */
    private function hasPaidForStream($userId, $streamId)
    {
        // This is a placeholder - implement your payment check logic
        return true;
    }
} 