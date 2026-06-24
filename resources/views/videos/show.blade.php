@extends('layouts.generic')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="video-detail">
                <div class="video-container">
                    <video 
                        src="{{ $video->video_url }}" 
                        poster="{{ $video->thumbnail_url }}"
                        controls
                        class="video-player"
                    ></video>
                </div>

                <div class="video-info">
                    <h1>{{ $video->title }}</h1>
                    <p class="description">{{ $video->description }}</p>
                    
                    <div class="meta">
                        <span class="views">
                            <i class="fas fa-eye"></i> {{ $video->views_count }} views
                        </span>
                        <span class="date">
                            <i class="fas fa-calendar"></i> {{ $video->created_at->diffForHumans() }}
                        </span>
                    </div>

                    <div class="actions">
                        <button class="action-btn like-btn {{ $video->is_liked ? 'active' : '' }}" data-video-id="{{ $video->id }}">
                            <i class="fas fa-heart"></i>
                            <span class="count">{{ $video->likes_count }}</span>
                        </button>
                        
                        <button class="action-btn share-btn" data-video-id="{{ $video->id }}">
                            <i class="fas fa-share"></i>
                        </button>
                    </div>
                </div>

                <div class="comments-section">
                    <h3>Comments ({{ $video->comments_count }})</h3>
                    
                    @auth
                    <form class="comment-form" action="{{ route('videos.comments.store', $video) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <textarea name="content" class="form-control" rows="2" placeholder="Add a comment..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary">Comment</button>
                    </form>
                    @endauth

                    <div class="comments-list">
                        @foreach($video->comments as $comment)
                        <div class="comment">
                            <div class="comment-header">
                                <img src="{{ $comment->user->avatar_url }}" alt="{{ $comment->user->name }}" class="avatar">
                                <div class="comment-info">
                                    <h4>{{ $comment->user->name }}</h4>
                                    <span class="date">{{ $comment->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <p class="comment-content">{{ $comment->content }}</p>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.video-detail {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.video-container {
    position: relative;
    width: 100%;
    padding-top: 56.25%; /* 16:9 Aspect Ratio */
}

.video-player {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.video-info {
    padding: 20px;
}

.video-info h1 {
    margin: 0 0 10px;
    font-size: 1.5rem;
}

.description {
    color: #666;
    margin-bottom: 15px;
}

.meta {
    display: flex;
    gap: 20px;
    color: #666;
    font-size: 0.9rem;
    margin-bottom: 15px;
}

.meta i {
    margin-right: 5px;
}

.actions {
    display: flex;
    gap: 20px;
    margin-bottom: 20px;
}

.action-btn {
    background: none;
    border: none;
    color: #666;
    font-size: 1.2rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    gap: 5px;
    padding: 8px;
    transition: color 0.2s;
}

.action-btn:hover {
    color: #333;
}

.action-btn.active {
    color: #ff2d55;
}

.comments-section {
    padding: 20px;
    border-top: 1px solid #eee;
}

.comments-section h3 {
    margin-bottom: 20px;
}

.comment-form {
    margin-bottom: 30px;
}

.comment-form textarea {
    resize: none;
    margin-bottom: 10px;
}

.comments-list {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.comment {
    display: flex;
    flex-direction: column;
    gap: 10px;
}

.comment-header {
    display: flex;
    align-items: center;
    gap: 10px;
}

.avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.comment-info h4 {
    margin: 0;
    font-size: 1rem;
}

.comment-info .date {
    color: #666;
    font-size: 0.8rem;
}

.comment-content {
    margin: 0;
    color: #333;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Like button functionality
    const likeBtn = document.querySelector('.like-btn');
    if (likeBtn) {
        likeBtn.addEventListener('click', async function() {
            const videoId = this.dataset.videoId;
            try {
                const response = await fetch(`/videos/${videoId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.success) {
                    this.classList.toggle('active');
                    const countSpan = this.querySelector('.count');
                    countSpan.textContent = data.likes_count;
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    }

    // Share button functionality
    const shareBtn = document.querySelector('.share-btn');
    if (shareBtn) {
        shareBtn.addEventListener('click', async function() {
            const videoId = this.dataset.videoId;
            try {
                const response = await fetch(`/videos/${videoId}/share`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                });
                const data = await response.json();
                
                if (data.success) {
                    const shareUrl = data.share_url;
                    if (navigator.share) {
                        navigator.share({
                            title: 'Check out this video!',
                            url: shareUrl
                        });
                    } else {
                        navigator.clipboard.writeText(shareUrl);
                        alert('Link copied to clipboard!');
                    }
                }
            } catch (error) {
                console.error('Error:', error);
            }
        });
    }
});
</script>
@endpush
@endsection 