@extends('layouts.generic')

@section('content')
<div class="tiktok-style-feed">
    <div class="video-container">
    @foreach($videos as $video)
        <div class="video-item" data-video-id="{{ $video->id }}">
            <video 
                src="{{ $video->video_url }}" 
                poster="{{ $video->thumbnail_url }}"
                loop
                playsinline
                class="video-player"
                preload="metadata"
            ></video>
            
            <div class="video-overlay">
                <div class="user-info">
                    <img src="{{ $video->user->avatar ?? asset('img/default-avatar.png') }}" alt="{{ $video->user->name }}" class="user-avatar">
                    <div class="user-details">
                        <h4>{{ $video->user->name }}</h4>
                        <p>{{ '@' . $video->user->username }}</p>
                    </div>
                    <button class="follow-btn" data-user-id="{{ $video->user_id }}">Follow</button>
                </div>
                
                <div class="video-caption">
                    <p>{{ $video->title }}</p>
                    <p class="video-description">{{ $video->description }}</p>
                </div>
            </div>
                
            <div class="video-actions">
                <div class="action-item">
                    <button class="action-btn like-btn {{ $video->isLikedBy(auth()->user()) ? 'active' : '' }}" data-video-id="{{ $video->id }}">
                        <i class="fas fa-heart"></i>
                    </button>
                    <span class="count">{{ $video->likes_count }}</span>
                </div>
                
                <div class="action-item">
                    <button class="action-btn comment-btn" data-video-id="{{ $video->id }}">
                        <i class="fas fa-comment"></i>
                    </button>
                    <span class="count">{{ $video->comments_count }}</span>
                </div>
                    
                <div class="action-item">
                    <button class="action-btn share-btn" data-video-id="{{ $video->id }}">
                        <i class="fas fa-share"></i>
                    </button>
                    <span class="count">{{ $video->shares_count ?? 0 }}</span>
                </div>
            </div>
        </div>
    @endforeach
    </div>
    
    <!-- Comments Sidebar (Initially Hidden) -->
    <div class="comments-sidebar">
        <div class="comments-header">
            <h3>Comments</h3>
            <button class="close-comments"><i class="fas fa-times"></i></button>
        </div>
        <div class="comments-list"></div>
        <div class="comment-form">
            <input type="text" placeholder="Add a comment..." class="comment-input">
            <button class="send-comment-btn"><i class="fas fa-paper-plane"></i></button>
        </div>
    </div>
</div>

@push('styles')
<style>
:root {
    --video-width: 100vw;
    --video-height: calc(100vh - 56px); /* Adjust based on your header height */
}

body, html {
    margin: 0;
    padding: 0;
    overflow: hidden;
}

.tiktok-style-feed {
    position: relative;
    width: 100%;
    height: 100vh;
    background: #000;
    overflow: hidden;
}

.video-container {
    width: 100%;
    height: 100%;
    scroll-snap-type: y mandatory;
    overflow-y: scroll;
    overflow-x: hidden;
    -webkit-overflow-scrolling: touch;
}

.video-item {
    position: relative;
    width: 100%;
    height: var(--video-height);
    scroll-snap-align: start;
    scroll-snap-stop: always;
}

.video-player {
    width: 100%;
    height: 100%;
    object-fit: contain;
    background-color: #000;
}

/* Desktop styles for fullscreen mode */
@media (min-width: 768px) {
    .video-player {
        object-fit: contain;
        max-height: 100vh;
        width: 100%;
    }
    
    .video-item.fullscreen .video-player {
        position: fixed;
        top: 0;
        left: 0;
        width: 100vw;
        height: 100vh;
        z-index: 1000;
    }
}

.video-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 100%;
    padding: 20px;
    background: linear-gradient(transparent, rgba(0,0,0,0.7));
    color: white;
    z-index: 2;
}

.user-info {
    display: flex;
    align-items: center;
    margin-bottom: 10px;
}

.user-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
    border: 2px solid white;
}

.user-details {
    flex-grow: 1;
}

.user-details h4 {
    margin: 0;
    font-size: 16px;
    font-weight: bold;
}

.user-details p {
    margin: 0;
    font-size: 14px;
    opacity: 0.8;
}

.follow-btn {
    background-color: #ff4d4d;
    color: white;
    border: none;
    border-radius: 4px;
    padding: 5px 15px;
    font-size: 14px;
    cursor: pointer;
}

.video-caption {
    margin-bottom: 15px;
}

.video-description {
    font-size: 14px;
    opacity: 0.8;
    margin-top: 5px;
}

.video-actions {
    position: absolute;
    right: 10px;
    bottom: 100px;
    display: flex;
    flex-direction: column;
    z-index: 3;
}

.action-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    margin-bottom: 20px;
}

.action-btn {
    background: transparent;
    border: none;
    color: white;
    font-size: 24px;
    cursor: pointer;
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    background-color: rgba(0,0,0,0.4);
}

.action-btn.active {
    color: #ff4d4d;
}

.count {
    color: white;
    font-size: 12px;
    margin-top: 5px;
}

.comments-sidebar {
    position: fixed;
    right: -100%;
    top: 0;
    width: 100%;
    height: 100vh;
    background-color: white;
    z-index: 1000;
    transition: right 0.3s ease;
    display: flex;
    flex-direction: column;
}

@media (min-width: 768px) {
    .comments-sidebar {
        width: 400px;
    }
}

.comments-sidebar.active {
    right: 0;
}

.comments-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px;
    border-bottom: 1px solid #eee;
}

.comments-header h3 {
    margin: 0;
}

.close-comments {
    background: transparent;
    border: none;
    font-size: 18px;
    cursor: pointer;
}

.comments-list {
    flex-grow: 1;
    overflow-y: auto;
    padding: 15px;
}

.comment-form {
    display: flex;
    padding: 15px;
    border-top: 1px solid #eee;
}

.comment-input {
    flex-grow: 1;
    border: 1px solid #ddd;
    border-radius: 20px;
    padding: 10px 15px;
    margin-right: 10px;
}

.send-comment-btn {
    background-color: #ff4d4d;
    color: white;
    border: none;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const videoItems = document.querySelectorAll('.video-item');
    const videoPlayers = document.querySelectorAll('.video-player');
    const commentBtns = document.querySelectorAll('.comment-btn');
    const closeCommentsBtn = document.querySelector('.close-comments');
    const commentsSidebar = document.querySelector('.comments-sidebar');
    const likeBtns = document.querySelectorAll('.like-btn');
    const shareBtns = document.querySelectorAll('.share-btn');
    const commentInput = document.querySelector('.comment-input');
    const sendCommentBtn = document.querySelector('.send-comment-btn');
    
    let currentVideoIndex = 0;
    let observer;
    
    // Initialize Intersection Observer to detect when videos are in view
    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const video = entry.target;
                
                if (entry.isIntersecting) {
                    // Play video when it's in view
                    video.muted = true; // Start muted to comply with autoplay policies
                    video.play().then(() => {
                        // Once playing, unmute if user has interacted with the page
                        if (document.documentElement.classList.contains('user-interacted')) {
                            video.muted = false;
                        }
                    }).catch(error => {
                        console.error('Error playing video:', error);
                    });
                    
                    // Update current video index
                    currentVideoIndex = Array.from(videoPlayers).indexOf(video);
                } else {
                    // Pause when not in view
                    video.pause();
                }
            });
        }, { threshold: 0.7 }); // At least 70% of the video must be visible
        
        // Observe all videos
        videoPlayers.forEach(video => {
            observer.observe(video);
        });
    }
    
    // Handle user interaction to enable sound
    document.addEventListener('click', function() {
        document.documentElement.classList.add('user-interacted');
        
        // Unmute current video after user interaction
        if (videoPlayers[currentVideoIndex]) {
            videoPlayers[currentVideoIndex].muted = false;
        }
    }, { once: true });
    
    // Handle video container scroll
    const videoContainer = document.querySelector('.video-container');
    videoContainer.addEventListener('scroll', function() {
        // Debounce the scroll event
        clearTimeout(window.scrollTimeout);
        window.scrollTimeout = setTimeout(() => {
            // Find which video is most visible
            const containerHeight = videoContainer.clientHeight;
            const scrollTop = videoContainer.scrollTop;
            
            // Calculate which video should be playing based on scroll position
            const newIndex = Math.round(scrollTop / containerHeight);
            
            if (newIndex !== currentVideoIndex && videoPlayers[newIndex]) {
                // Pause current video
                if (videoPlayers[currentVideoIndex]) {
                    videoPlayers[currentVideoIndex].pause();
                }
                
                // Play new video
                const newVideo = videoPlayers[newIndex];
                newVideo.play().catch(error => {
                    console.error('Error playing video after scroll:', error);
                });
                
                // Update current index
                currentVideoIndex = newIndex;
            }
        }, 50);
    });
    
    // Toggle fullscreen on desktop
    if (window.innerWidth >= 768) {
        videoPlayers.forEach((video, index) => {
            video.addEventListener('click', function() {
                videoItems[index].classList.toggle('fullscreen');
                
                if (document.fullscreenElement) {
                    document.exitFullscreen();
                } else if (video.requestFullscreen) {
                    video.requestFullscreen();
                }
            });
        });
    }
    
    // Handle comments sidebar
    commentBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const videoId = this.getAttribute('data-video-id');
            
            // Load comments for this video (AJAX call would go here)
            loadComments(videoId);
            
            // Show comments sidebar
            commentsSidebar.classList.add('active');
        });
    });
    
    closeCommentsBtn.addEventListener('click', function() {
        commentsSidebar.classList.remove('active');
    });
    
    // Handle likes
    likeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const videoId = this.getAttribute('data-video-id');
            const isActive = this.classList.contains('active');
            
            // Toggle active state
            this.classList.toggle('active');
            
            // Update count
            const countElement = this.nextElementSibling;
            let count = parseInt(countElement.textContent);
            
            if (isActive) {
                // Unlike
                count--;
                countElement.textContent = count;
                
                // Send unlike request
                fetch(`/videos/${videoId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
            } else {
                // Like
                count++;
                countElement.textContent = count;
                
                // Send like request
                fetch(`/videos/${videoId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    }
                });
            }
        });
    });
    
    // Handle shares
    shareBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const videoId = this.getAttribute('data-video-id');
            
            // Show share options (could be a modal or dropdown)
            showShareOptions(videoId);
        });
    });
    
    // Handle comment submission
    sendCommentBtn.addEventListener('click', function() {
        const comment = commentInput.value.trim();
        if (comment) {
            const videoId = document.querySelector('.comment-form').getAttribute('data-video-id');
            submitComment(videoId, comment);
        }
    });
    
    commentInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            const comment = this.value.trim();
            if (comment) {
                const videoId = document.querySelector('.comment-form').getAttribute('data-video-id');
                submitComment(videoId, comment);
            }
        }
    });
    
    // Helper functions
    function loadComments(videoId) {
        const commentsList = document.querySelector('.comments-list');
        commentsList.innerHTML = '<p>Loading comments...</p>';
        
        // Set the video ID on the comment form
        document.querySelector('.comment-form').setAttribute('data-video-id', videoId);
        
        // AJAX call to load comments
        fetch(`/videos/${videoId}/comments`)
            .then(response => response.json())
            .then(data => {
                if (data.comments && data.comments.length > 0) {
                    commentsList.innerHTML = '';
                    data.comments.forEach(comment => {
                        const commentElement = createCommentElement(comment);
                        commentsList.appendChild(commentElement);
                    });
                } else {
                    commentsList.innerHTML = '<p>No comments yet. Be the first to comment!</p>';
                }
            })
            .catch(error => {
                console.error('Error loading comments:', error);
                commentsList.innerHTML = '<p>Error loading comments. Please try again.</p>';
            });
    }
    
    function createCommentElement(comment) {
        const div = document.createElement('div');
        div.className = 'comment-item';
        div.innerHTML = `
            <div class="comment-user">
                <img src="${comment.user.avatar || '/img/default-avatar.png'}" alt="${comment.user.name}" class="comment-avatar">
                <div>
                    <strong>${comment.user.name}</strong>
                    <small>${formatDate(comment.created_at)}</small>
                </div>
            </div>
            <p class="comment-content">${comment.content}</p>
        `;
        return div;
    }
    
    function submitComment(videoId, comment) {
        fetch(`/videos/${videoId}/comments`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ content: comment })
        })
        .then(response => response.json())
        .then(data => {
            if (data.comment) {
                // Add the new comment to the list
                const commentsList = document.querySelector('.comments-list');
                const commentElement = createCommentElement(data.comment);
                commentsList.prepend(commentElement);
                
                // Clear the input
                commentInput.value = '';
                
                // Update the comment count on the video
                const videoItem = document.querySelector(`.video-item[data-video-id="${videoId}"]`);
                const countElement = videoItem.querySelector('.comment-btn').nextElementSibling;
                let count = parseInt(countElement.textContent);
                count++;
                countElement.textContent = count;
            }
        })
        .catch(error => {
            console.error('Error submitting comment:', error);
        });
    }
    
    function showShareOptions(videoId) {
        // This could be a modal or dropdown with share options
        const shareOptions = [
            { platform: 'facebook', icon: 'fab fa-facebook', label: 'Facebook' },
            { platform: 'twitter', icon: 'fab fa-twitter', label: 'Twitter' },
            { platform: 'whatsapp', icon: 'fab fa-whatsapp', label: 'WhatsApp' },
            { platform: 'telegram', icon: 'fab fa-telegram', label: 'Telegram' }
        ];
        
        // Create a simple dropdown for now
        const shareBtn = document.querySelector(`.video-item[data-video-id="${videoId}"] .share-btn`);
        const dropdown = document.createElement('div');
        dropdown.className = 'share-dropdown';
        dropdown.style.position = 'absolute';
        dropdown.style.backgroundColor = 'white';
        dropdown.style.borderRadius = '8px';
        dropdown.style.padding = '10px';
        dropdown.style.boxShadow = '0 2px 10px rgba(0,0,0,0.2)';
        dropdown.style.zIndex = '1000';
        
        shareOptions.forEach(option => {
            const button = document.createElement('button');
            button.className = 'share-option';
            button.innerHTML = `<i class="${option.icon}"></i> ${option.label}`;
            button.style.display = 'block';
            button.style.width = '100%';
            button.style.padding = '8px 12px';
            button.style.backgroundColor = 'transparent';
            button.style.border = 'none';
            button.style.textAlign = 'left';
            button.style.cursor = 'pointer';
            
            button.addEventListener('click', function() {
                shareVideo(videoId, option.platform);
                document.body.removeChild(dropdown);
            });
            
            dropdown.appendChild(button);
        });
        
        // Position the dropdown near the share button
        const rect = shareBtn.getBoundingClientRect();
        dropdown.style.top = `${rect.bottom + window.scrollY}px`;
        dropdown.style.right = `${window.innerWidth - rect.right}px`;
        
        // Add to body and handle outside clicks
        document.body.appendChild(dropdown);
        
        setTimeout(() => {
            document.addEventListener('click', function closeDropdown(e) {
                if (!dropdown.contains(e.target) && e.target !== shareBtn) {
                    if (document.body.contains(dropdown)) {
                        document.body.removeChild(dropdown);
                    }
                    document.removeEventListener('click', closeDropdown);
                }
            });
        }, 0);
    }
    
    function shareVideo(videoId, platform) {
        fetch(`/videos/${videoId}/share`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ platform })
        })
        .then(response => response.json())
        .then(data => {
            // Update share count
            const videoItem = document.querySelector(`.video-item[data-video-id="${videoId}"]`);
            const countElement = videoItem.querySelector('.share-btn').nextElementSibling;
            let count = parseInt(countElement.textContent);
            count++;
            countElement.textContent = count;
            
            // Open share URL based on platform
            const videoUrl = `${window.location.origin}/videos/${videoId}`;
            let shareUrl;
            
            switch(platform) {
                case 'facebook':
                    shareUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(videoUrl)}`;
                    break;
                case 'twitter':
                    shareUrl = `https://twitter.com/intent/tweet?url=${encodeURIComponent(videoUrl)}`;
                    break;
                case 'whatsapp':
                    shareUrl = `https://api.whatsapp.com/send?text=${encodeURIComponent(videoUrl)}`;
                    break;
                case 'telegram':
                    shareUrl = `https://t.me/share/url?url=${encodeURIComponent(videoUrl)}`;
                    break;
            }
            
            if (shareUrl) {
                window.open(shareUrl, '_blank');
            }
        })
        .catch(error => {
            console.error('Error sharing video:', error);
        });
    }
    
    function formatDate(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const diffTime = Math.abs(now - date);
        const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24));
        
        if (diffDays < 1) {
            const diffHours = Math.floor(diffTime / (1000 * 60 * 60));
            if (diffHours < 1) {
                const diffMinutes = Math.floor(diffTime / (1000 * 60));
                return `${diffMinutes} minute${diffMinutes !== 1 ? 's' : ''} ago`;
            }
            return `${diffHours} hour${diffHours !== 1 ? 's' : ''} ago`;
        } else if (diffDays < 7) {
            return `${diffDays} day${diffDays !== 1 ? 's' : ''} ago`;
        } else {
            return date.toLocaleDateString();
        }
    }
});
</script>
@endpush
@endsection 