@extends('layouts.generic')
@section('page_title', __('Video Reels'))

{{-- Page specific CSS --}}
@section('styles')
    {!!
        Minify::stylesheet([
            '/libs/swiper/swiper-bundle.min.css',
            '/libs/photoswipe/dist/photoswipe.css',
            '/css/pages/checkout.css',
            '/libs/photoswipe/dist/default-skin/default-skin.css',
            '/css/pages/feed.css',
            '/css/posts/post.css',
            '/css/pages/search.css',
         ])->withFullUrl()
    !!}
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@stop

{{-- Page specific JS --}}
@section('scripts')
    {!!
        Minify::javascript([
            '/js/PostsPaginator.js',
            '/js/CommentsPaginator.js',
            '/js/Post.js',
            '/js/SuggestionsSlider.js',
            '/js/pages/lists.js',
            '/js/pages/feed.js',
            '/js/pages/checkout.js',
            '/libs/swiper/swiper-bundle.min.js',
            '/js/plugins/media/photoswipe.js',
            '/libs/photoswipe/dist/photoswipe-ui-default.min.js',
            '/js/plugins/media/mediaswipe.js',
            '/js/plugins/media/mediaswipe-loader.js',
         ])->withFullUrl()
    !!}
    
    <!-- Reels JavaScript -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const videoItems = document.querySelectorAll('.video-item');
            const videoPlayers = document.querySelectorAll('.video-player');
            let currentVideoIndex = 0;
            let isScrolling = false;
            
            // Initialize all videos
            videoPlayers.forEach((video, index) => {
                video.setAttribute('playsinline', '');
                video.setAttribute('webkit-playsinline', '');
                video.muted = true; // Start muted for autoplay
                video.load();
                
                // Click to play/pause
                video.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (this.paused) {
                        this.play();
                    } else {
                        this.pause();
                    }
                });
                
                // Double click to toggle sound
                let clickCount = 0;
                video.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    clickCount++;
                    setTimeout(() => {
                        if (clickCount === 2) {
                            toggleSound(this);
                        }
                        clickCount = 0;
                    }, 300);
                });
            });
            
            videoPlayers.forEach((video, index) => {
    video.setAttribute('playsinline', '');
    video.setAttribute('webkit-playsinline', '');
    video.muted = true; // Start muted for autoplay
    video.load();
    
    // Click to play/pause
    video.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        if (this.paused) {
            this.play();
        } else {
            this.pause();
        }
    });
    
    // Double click to toggle sound
    let clickCount = 0;
    video.addEventListener('click', function(e) {
        e.preventDefault();
        e.stopPropagation();
        clickCount++;
        setTimeout(() => {
            if (clickCount === 2) {
                toggleSound(this);
            }
            clickCount = 0;
        }, 300);
    });
});


            let viewedVideos = new Set();
            // Intersection Observer for autoplay
const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        const video = entry.target;
        if (entry.isIntersecting && entry.intersectionRatio > 0.7) {
            pauseAllVideosExcept(video);
            video.currentTime = 0;
            video.play().catch(e => console.log('Autoplay failed:', e));
            
             // 🔥 INCREMENT VIEWS WHEN VIDEO STARTS PLAYING
            const videoId = video.closest('.video-item').getAttribute('data-video-id');
            if (videoId) {
                incrementVideoViews(videoId);
            }

            // Update progress bar
            const progressBar = video.closest('.video-item').querySelector('.progress');
            if (progressBar) {
                startProgressBar(video, progressBar);
            }
        } else {
            video.pause();
        }
    });
}, {
    threshold: [0.7],
    rootMargin: '-10% 0px'
});
            
            // Observe all videos
            videoPlayers.forEach(video => observer.observe(video));

            function handleVideoUrlParameter() {
                const urlParams = new URLSearchParams(window.location.search);
                const videoId = urlParams.get('video');
                
                if (videoId) {
                    // Find the video item with the specified ID
                    const targetVideoItem = document.querySelector(`.video-item[data-video-id="${videoId}"]`);
                    
                    if (targetVideoItem) {
                        // Get the index of this video in the list
                        const allVideoItems = document.querySelectorAll('.video-item');
                        const videoIndex = Array.from(allVideoItems).indexOf(targetVideoItem);
                        
                        if (videoIndex !== -1) {
                            // Update current video index
                            currentVideoIndex = videoIndex;
                            
                            // Scroll to the specific video immediately
                            setTimeout(() => {
                                targetVideoItem.scrollIntoView({ 
                                    behavior: 'instant',
                                    block: 'start'
                                });
                                
                                // Ensure the video starts playing
                                const video = targetVideoItem.querySelector('.video-player');
                                if (video) {
                                    pauseAllVideosExcept(video);
                                    video.currentTime = 0;
                                    video.play().catch(e => console.log('Autoplay failed:', e));
                                    
                                    // Update progress bar
                                    const progressBar = targetVideoItem.querySelector('.progress');
                                    if (progressBar) {
                                        startProgressBar(video, progressBar);
                                    }
                                }
                            }, 100);
                        }
                    } else {
                        console.log(`Video with ID ${videoId} not found`);
                    }
                }
            }
            
            // Call this function after all videos are initialized
            handleVideoUrlParameter();
            
            // Touch/scroll navigation
            let touchStartY = 0;
            let touchEndY = 0;
            
            document.addEventListener('touchstart', e => {
                touchStartY = e.changedTouches[0].screenY;
            });
            
            document.addEventListener('touchend', e => {
                touchEndY = e.changedTouches[0].screenY;
                handleSwipe();
            });
            
            // Mouse wheel navigation
            document.addEventListener('wheel', e => {
                if (isScrolling) return;
                e.preventDefault();
                
                const direction = e.deltaY > 0 ? 1 : -1;
                scrollToVideo(currentVideoIndex + direction);
                
                isScrolling = true;
                setTimeout(() => isScrolling = false, 800);
            });
            
            function handleSwipe() {
                const swipeDistance = touchStartY - touchEndY;
                if (Math.abs(swipeDistance) < 50) return;
                
                const direction = swipeDistance > 0 ? 1 : -1;
                scrollToVideo(currentVideoIndex + direction);
            }
            
            function scrollToVideo(index) {
                if (index >= 0 && index < videoItems.length) {
                    videoItems[index].scrollIntoView({ 
                        behavior: 'smooth',
                        block: 'start'
                    });
                    currentVideoIndex = index;
                }
            }
            
            function pauseAllVideosExcept(currentVideo) {
                videoPlayers.forEach(video => {
                    if (video !== currentVideo) {
                        video.pause();
                        video.currentTime = 0;
                    }
                });
            }
            
            function toggleSound(video) {
                video.muted = !video.muted;
                const soundBtn = video.closest('.video-wrapper').querySelector('.sound-control-btn');
                if (soundBtn) {
                    soundBtn.innerHTML = video.muted ? 
                        '<i class="fas fa-volume-mute"></i>' : 
                        '<i class="fas fa-volume-up"></i>';
                }
            }
            
            function startProgressBar(video, progressBar) {
                const updateProgress = () => {
                    if (!video.paused && video.duration > 0) {
                        const progress = (video.currentTime / video.duration) * 100;
                        progressBar.style.width = progress + '%';
                        
                        if (progress < 100 && !video.paused) {
                            requestAnimationFrame(updateProgress);
                        }
                    }
                };
                requestAnimationFrame(updateProgress);
            }
            
            // Add sound control buttons
            videoPlayers.forEach(video => {
                const wrapper = video.closest('.video-wrapper');
                const soundBtn = document.createElement('button');
                soundBtn.className = 'sound-control-btn';
                soundBtn.innerHTML = '<i class="fas fa-volume-mute"></i>';
                soundBtn.onclick = () => toggleSound(video);
                wrapper.appendChild(soundBtn);
            });
            
            // Like button functionality with API call
            document.querySelectorAll('.like-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const videoId = this.getAttribute('data-video-id');
                    const countSpan = this.closest('.action-item').querySelector('.action-count');
                    const icon = this.querySelector('i');
                    let count = parseInt(countSpan.textContent);
                    
                    // Toggle UI immediately for better UX
                    const wasLiked = this.classList.contains('liked');
                    
                    if (!wasLiked) {
                        this.classList.add('liked');
                        icon.classList.remove('far');
                        icon.classList.add('fas');
                        this.style.color = '#ff1744';
                        countSpan.textContent = count + 1;
                    } else {
                        this.classList.remove('liked');
                        icon.classList.remove('fas');
                        icon.classList.add('far');
                        this.style.color = '#fff';
                        countSpan.textContent = count - 1;
                    }
                    
                    // Make API call
                    fetch(`/videos/${videoId}/like`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Update with actual count from server
                            countSpan.textContent = data.likes_count;
                            
                            if (data.is_liked) {
                                this.classList.add('liked');
                                icon.classList.remove('far');
                                icon.classList.add('fas');
                                this.style.color = '#ff1744';
                            } else {
                                this.classList.remove('liked');
                                icon.classList.remove('fas');
                                icon.classList.add('far');
                                this.style.color = '#fff';
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        // Revert UI changes on error
                        if (wasLiked) {
                            this.classList.add('liked');
                            icon.classList.remove('far');
                            icon.classList.add('fas');
                            this.style.color = '#ff1744';
                            countSpan.textContent = count;
                        } else {
                            this.classList.remove('liked');
                            icon.classList.remove('fas');
                            icon.classList.add('far');
                            this.style.color = '#fff';
                            countSpan.textContent = count;
                        }
                    });
                });
            });
            
            // Comments sidebar functionality
            const commentsSidebar = document.getElementById('comments-sidebar');
            const closeCommentsBtn = document.querySelector('.close-comments');
            
            document.querySelectorAll('.comment-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    commentsSidebar.classList.add('active');
                    const videoId = this.getAttribute('data-video-id');
                    loadComments(videoId);
                });
            });
            
            closeCommentsBtn.addEventListener('click', function() {
                commentsSidebar.classList.remove('active');
            });
            
            // Close sidebar when clicking outside
            document.addEventListener('click', function(e) {
                if (commentsSidebar.classList.contains('active') && 
                    !commentsSidebar.contains(e.target) && 
                    !e.target.closest('.comment-btn')) {
                    commentsSidebar.classList.remove('active');
                }
            });
            
            // Share button functionality
            document.querySelectorAll('.share-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const videoId = this.getAttribute('data-video-id');
                    const countSpan = this.closest('.action-item').querySelector('.action-count');
                    let count = parseInt(countSpan.textContent);
                    
                    // Update count immediately
                    countSpan.textContent = count + 1;
                    
                    // Make API call
                    fetch(`/videos/${videoId}/share`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            countSpan.textContent = data.shares_count;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        countSpan.textContent = count; // Revert on error
                    });
                    
                    // Native share functionality
                    const videoUrl = `${window.location.origin}/videos/${videoId}`;
                    if (navigator.share) {
                        navigator.share({
                            title: 'Check out this video!',
                            url: videoUrl
                        });
                    } else {
                        // Fallback copy to clipboard
                        navigator.clipboard.writeText(videoUrl);
                        showNotification('Link copied to clipboard!');
                    }
                });
            });
            
            // Repost button functionality (Twitter-like)
            document.querySelectorAll('.repost-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    const videoId = this.getAttribute('data-video-id');
                    const countSpan = this.closest('.action-item').querySelector('.action-count');
                    const icon = this.querySelector('i');
                    let count = parseInt(countSpan.textContent);
                    
                    // Check if already reposted
                    const wasReposted = this.classList.contains('reposted');
                    
                    if (!wasReposted) {
                        if (confirm('Repost this video to your profile?')) {
                            this.classList.add('reposted');
                            this.style.color = '#17bf63';
                            countSpan.textContent = count + 1;
                            
                            // Make API call
                            fetch(`/videos/${videoId}/repost`, {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    countSpan.textContent = data.reposts_count;
                                    showNotification('Video reposted successfully!');
                                } else {
                                    // Revert on error
                                    this.classList.remove('reposted');
                                    this.style.color = '#fff';
                                    countSpan.textContent = count;
                                    showNotification(data.message || 'Failed to repost video');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                // Revert on error
                                this.classList.remove('reposted');
                                this.style.color = '#fff';
                                countSpan.textContent = count;
                                showNotification('Failed to repost video');
                            });
                        }
                    } else {
                        // Undo repost
                        if (confirm('Remove this repost?')) {
                            this.classList.remove('reposted');
                            this.style.color = '#fff';
                            countSpan.textContent = count - 1;
                            
                            // Make API call to undo repost
                            fetch(`/videos/${videoId}/unrepost`, {
                                method: 'DELETE',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    countSpan.textContent = data.reposts_count;
                                    showNotification('Repost removed successfully!');
                                } else {
                                    // Revert on error
                                    this.classList.add('reposted');
                                    this.style.color = '#17bf63';
                                    countSpan.textContent = count;
                                    showNotification(data.message || 'Failed to remove repost');
                                }
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                // Revert on error
                                this.classList.add('reposted');
                                this.style.color = '#17bf63';
                                countSpan.textContent = count;
                                showNotification('Failed to remove repost');
                            });
                        }
                    }
                });
            });
            
            // Upload button functionality
            document.querySelectorAll('.upload-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    window.location.href = '/videos/create';
                });
            });
            
            // Notification function
            function showNotification(message) {
                const notification = document.createElement('div');
                notification.className = 'notification';
                notification.textContent = message;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.classList.add('show');
                }, 100);
                
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }
            
            // ===== Profile Share Functionality =====
            const profileModal = document.getElementById('profile-modal');
            const profileModalClose = document.querySelector('.profile-modal-close');
            
            // Profile click functionality
            document.querySelectorAll('.user-info').forEach(userInfo => {
                userInfo.addEventListener('click', function(e) {
                    e.stopPropagation();
                    const userId = this.getAttribute('data-user-id');
                    const usernameForUrl = this.getAttribute('data-user-username') || (this.querySelector('.user-details p') && (this.querySelector('.user-details p').textContent || '').replace(/^@/, '').trim()) || '';
                    const userName = this.querySelector('.user-details h4').textContent;
                    const userUsername = this.querySelector('.user-details p').textContent;
                    const userAvatar = this.querySelector('.user-avatar-initials').style.background;
                    const userInitials = this.querySelector('.user-avatar-initials').textContent;
                    
                    openProfileModal(userId, userName, userUsername, userAvatar, userInitials, usernameForUrl);
                });
            });
            
            function openProfileModal(userId, userName, userUsername, userAvatar, userInitials, usernameForUrl) {
                if (profileModal) {
                    // Update modal content
                    document.getElementById('profile-modal-avatar').style.background = userAvatar;
                    document.getElementById('profile-modal-avatar').textContent = userInitials;
                    document.getElementById('profile-modal-name').textContent = userName;
                    document.getElementById('profile-modal-username').textContent = userUsername;
                    
                    // Username for URLs (from data-user-username or strip @ from display text)
                    const urlUsername = (usernameForUrl || (userUsername || '').replace(/^@/, '').trim());
                    
                    // Set user ID and username for sharing / view profile
                    document.getElementById('share-profile-btn').setAttribute('data-user-id', userId);
                    document.getElementById('share-profile-btn').setAttribute('data-username', urlUsername);
                    document.getElementById('view-profile-btn').setAttribute('data-user-id', userId);
                    document.getElementById('view-profile-btn').setAttribute('data-username', urlUsername);
                    
                    profileModal.classList.add('active');
                }
            }
            
            if (profileModalClose) {
                profileModalClose.addEventListener('click', function() {
                    profileModal.classList.remove('active');
                });
            }
            
            // Close profile modal when clicking outside
            if (profileModal) {
                document.addEventListener('click', function(e) {
                    if (profileModal.classList.contains('active') && 
                        e.target === profileModal) {
                        profileModal.classList.remove('active');
                    }
                });
            }
            
            // Share profile functionality (use username for URL so shared links work)
            document.getElementById('share-profile-btn').addEventListener('click', function() {
                const userId = this.getAttribute('data-user-id');
                const userName = document.getElementById('profile-modal-name').textContent;
                const username = this.getAttribute('data-username') || (document.getElementById('profile-modal-username').textContent || '').replace(/^@/, '').trim();
                const profileUrl = username ? window.location.origin + '/' + encodeURIComponent(username) : window.location.origin + '/' + encodeURIComponent(userName);
                
                if (navigator.share) {
                    navigator.share({
                        title: `Check out ${userName}'s profile!`,
                        text: `See amazing videos from ${userName}`,
                        url: profileUrl
                    }).then(() => {
                        console.log('Profile shared successfully');
                    }).catch(err => {
                        console.log('Error sharing profile:', err);
                        fallbackShareProfile(profileUrl, userName);
                    });
                } else {
                    fallbackShareProfile(profileUrl, userName);
                }
            });
            
            function fallbackShareProfile(profileUrl, userName) {
                navigator.clipboard.writeText(profileUrl).then(() => {
                    showShareNotification(`${userName}'s profile link copied to clipboard!`);
                }).catch(err => {
                    console.error('Could not copy text: ', err);
                    showShareNotification('Unable to copy link. Please try again.');
                });
            }
            
            function showShareNotification(message) {
                const notification = document.createElement('div');
                notification.className = 'share-notification';
                notification.textContent = message;
                document.body.appendChild(notification);
                
                setTimeout(() => {
                    notification.classList.add('show');
                }, 100);
                
                setTimeout(() => {
                    notification.classList.remove('show');
                    setTimeout(() => {
                        document.body.removeChild(notification);
                    }, 300);
                }, 3000);
            }
            
            // View profile functionality (use username for URL, not display name)
            document.getElementById('view-profile-btn').addEventListener('click', function() {
                const username = this.getAttribute('data-username');
                if (username) {
                    window.location.href = '/' + encodeURIComponent(username);
                }
            });
            
            // ===== Header Menu Functionality =====
            const headerMenuBtn = document.getElementById('header-menu-btn');
            const headerModal = document.getElementById('header-modal');
            const headerModalClose = document.querySelector('.header-modal-close');
            
            if (headerMenuBtn && headerModal) {
                headerMenuBtn.addEventListener('click', function() {
                    headerModal.classList.add('active');
                });
            }
            
            if (headerModalClose) {
                headerModalClose.addEventListener('click', function() {
                    headerModal.classList.remove('active');
                });
            }
            
            // Close modal when clicking outside
            if (headerModal) {
                document.addEventListener('click', function(e) {
                    if (headerModal.classList.contains('active') && 
                        e.target === headerModal) {
                        headerModal.classList.remove('active');
                    }
                });
            }



// 🔥 ADD THIS NEW FUNCTION FOR VIEWS TRACKING
function incrementVideoViews(videoId) {
    console.log('📈 Incrementing views for video:', videoId);
    
    fetch(`/videos/${videoId}/views`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            console.log('✅ Views incremented successfully:', data.views_count);
            console.log('📊 Response data:', data);
        } else {
            console.error('❌ Views increment failed:', data);
        }
    })
    .catch(error => {
        console.error('❌ Error incrementing views:', error);
    });
}
            
            // Update header profile link dynamically
            const headerProfileLink = document.querySelector('a[href="/profile"]');
            if (headerProfileLink) {
                const currentUserName = '{{ Auth::user()->name ?? "Profile" }}';
                headerProfileLink.href = '/' + currentUserName;
            }
            
            // ===== Search Functionality =====
            const searchBtn = document.getElementById('search-btn');
            const searchModal = document.getElementById('search-modal');
            const searchModalClose = document.querySelector('.search-modal-close');
            const searchInput = document.getElementById('search-input');
            const searchResults = document.getElementById('search-results');
            
            if (searchBtn && searchModal) {
                searchBtn.addEventListener('click', function() {
                    searchModal.classList.add('active');
                    if (searchInput) {
                        setTimeout(() => searchInput.focus(), 300);
                    }
                });
            }
            
            if (searchModalClose) {
                searchModalClose.addEventListener('click', function() {
                    searchModal.classList.remove('active');
                });
            }
            
            // Close search modal when clicking outside
            if (searchModal) {
                document.addEventListener('click', function(e) {
                    if (searchModal.classList.contains('active') && 
                        e.target === searchModal) {
                        searchModal.classList.remove('active');
                    }
                });
            }
            
            // ===== Comments Header Close Button =====
            const commentsCloseHeader = document.getElementById('comments-close-header');
            
            // Show/hide header close button when comments open/close
            const originalCommentsToggle = () => {
                if (commentsSidebar && commentsCloseHeader) {
                    const observer = new MutationObserver(function(mutations) {
                        mutations.forEach(function(mutation) {
                            if (mutation.type === 'attributes' && mutation.attributeName === 'class') {
                                if (commentsSidebar.classList.contains('active')) {
                                    commentsCloseHeader.classList.add('active');
                                } else {
                                    commentsCloseHeader.classList.remove('active');
                                }
                            }
                        });
                    });
                    
                    observer.observe(commentsSidebar, {
                        attributes: true,
                        attributeFilter: ['class']
                    });
                }
            };
            
            originalCommentsToggle();
            
            if (commentsCloseHeader) {
                commentsCloseHeader.addEventListener('click', function() {
                    if (commentsSidebar) {
                        commentsSidebar.classList.remove('active');
                    }
                });
            }
            
            // Search input functionality with API
            if (searchInput && searchResults) {
                let searchTimeout;
                searchInput.addEventListener('input', function() {
                    clearTimeout(searchTimeout);
                    const query = this.value.trim();
                    
                    if (query.length > 2) {
                        searchTimeout = setTimeout(() => {
                            performSearch(query);
                        }, 500);
                    } else {
                        searchResults.innerHTML = '<div class="search-placeholder">Type at least 3 characters to search...</div>';
                    }
                });
            }
            
            function performSearch(query) {
                if (!searchResults) return;
                
                searchResults.innerHTML = '<div class="search-loading">Searching...</div>';
                
                // Make API call to search
                fetch(`/search/videos?q=${encodeURIComponent(query)}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.videos.length > 0) {
                        const resultsHtml = data.videos.map((video, index) => `
                            <div class="search-result-item" onclick="goToVideo('${video.id}')">
                                <div class="search-result-info">
                                    <h4>${video.title}</h4>
                                    <p>By ${video.user.name}</p>
                                </div>
                            </div>
                        `).join('');
                        searchResults.innerHTML = resultsHtml;
                    } else {
                        searchResults.innerHTML = '<div class="no-search-results">No videos found for "' + query + '"</div>';
                    }
                })
                .catch(error => {
                    console.error('Search error:', error);
                    searchResults.innerHTML = '<div class="no-search-results">Search failed. Please try again.</div>';
                });
            }
            
            // Navigate to specific video
            window.goToVideo = function(videoId) {
                const videoElement = document.querySelector(`.video-item[data-video-id="${videoId}"]`);
                if (videoElement) {
                    videoElement.scrollIntoView({ behavior: 'smooth' });
                    if (searchModal) {
                        searchModal.classList.remove('active');
                    }
                }
            }
            
            // Comments functionality
            function loadComments(videoId) {
                const commentsList = document.getElementById('comments-list');
                commentsList.innerHTML = '<div class="loading">Loading comments...</div>';
                
                // Fetch comments from API
                fetch(`/videos/${videoId}/comments`)
                .then(response => response.json())
                .then(data => {
                    if (data.success && data.comments.length > 0) {
                        const commentsHtml = data.comments.map(comment => `
                            <div class="comment-item">
                                <div class="comment-avatar-initials" style="background: ${getRandomColor()};">${getUserInitials(comment.user.name)}</div>
                                <div class="comment-content">
                                    <strong>${comment.user.name}</strong>
                                    <p>${comment.content}</p>
                                    <span class="comment-time">${formatTime(comment.created_at)}</span>
                                </div>
                            </div>
                        `).join('');
                        commentsList.innerHTML = commentsHtml;
                    } else {
                        commentsList.innerHTML = '<div class="no-comments"><p>No comments yet. Be the first to comment!</p></div>';
                    }
                })
                .catch(error => {
                    console.error('Error loading comments:', error);
                    commentsList.innerHTML = '<div class="no-comments"><p>Failed to load comments.</p></div>';
                });
            }
            
            // Helper functions
            function getRandomColor() {
                const colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7'];
                return colors[Math.floor(Math.random() * colors.length)];
            }
            
            function getUserInitials(name) {
                const parts = name.split(' ');
                return parts.length >= 2 ? 
                    (parts[0][0] + parts[1][0]).toUpperCase() : 
                    name.substring(0, 2).toUpperCase();
            }
            
            function formatTime(dateString) {
                const date = new Date(dateString);
                const now = new Date();
                const diff = now - date;
                
                const minutes = Math.floor(diff / (1000 * 60));
                const hours = Math.floor(diff / (1000 * 60 * 60));
                const days = Math.floor(diff / (1000 * 60 * 60 * 24));
                
                if (minutes < 60) {
                    return `${minutes}m ago`;
                } else if (hours < 24) {
                    return `${hours}h ago`;
                } else {
                    return `${days}d ago`;
                }
            }
            
            // Post comment functionality
            const commentInput = document.getElementById('comment-input');
            const postCommentBtn = document.getElementById('post-comment');
            let currentVideoIdForComments = null;
            
            // Update current video ID when comments are opened
            document.querySelectorAll('.comment-btn').forEach(btn => {
                btn.addEventListener('click', function(e) {
                    currentVideoIdForComments = this.getAttribute('data-video-id');
                });
            });
            
            if (commentInput && postCommentBtn) {
                postCommentBtn.addEventListener('click', function() {
                    const comment = commentInput.value.trim();
                    if (comment && currentVideoIdForComments) {
                        // Disable button to prevent double submission
                        postCommentBtn.disabled = true;
                        postCommentBtn.textContent = 'Posting...';
                        
                        // Make API call to post comment
                        fetch(`/videos/${currentVideoIdForComments}/comments`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                content: comment
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                // Add comment to list
                                const commentsList = document.getElementById('comments-list');
                                const newComment = document.createElement('div');
                                newComment.className = 'comment-item';
                                newComment.innerHTML = `
                                    <div class="comment-avatar-initials" style="background: ${getRandomColor()};">${getUserInitials(data.comment.user.name)}</div>
                                    <div class="comment-content">
                                        <strong>${data.comment.user.name}</strong>
                                        <p>${data.comment.content}</p>
                                        <span class="comment-time">Just now</span>
                                    </div>
                                `;
                                
                                // Remove "no comments" message if exists
                                const noComments = commentsList.querySelector('.no-comments');
                                if (noComments) {
                                    noComments.remove();
                                }
                                
                                commentsList.insertBefore(newComment, commentsList.firstChild);
                                commentInput.value = '';
                                
                                // Update comment count in UI
                                const currentVideo = document.querySelector(`.video-item[data-video-id="${currentVideoIdForComments}"]`);
                                const commentCountSpan = currentVideo.querySelector('.comment-btn').closest('.action-item').querySelector('.action-count');
                                if (commentCountSpan) {
                                    const count = parseInt(commentCountSpan.textContent);
                                    commentCountSpan.textContent = count + 1;
                                }
                                
                                showNotification('Comment posted successfully!');
                            } else {
                                showNotification(data.message || 'Failed to post comment');
                            }
                        })
                        .catch(error => {
                            console.error('Error posting comment:', error);
                            showNotification('Failed to post comment');
                        })
                        .finally(() => {
                            // Re-enable button
                            postCommentBtn.disabled = false;
                            postCommentBtn.textContent = 'Post';
                        });
                    }
                });
                
                commentInput.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter' && !e.shiftKey) {
                        e.preventDefault();
                        postCommentBtn.click();
                    }
                });
            }
        });
    </script>
@stop

@section('content')
    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <!-- Comments Close Button in Header (appears when comments are open) -->
    <button id="comments-close-header" class="comments-close-header">
        <i class="fas fa-times"></i>
    </button>

    <!-- Transparent Header -->
{{--    <div class="transparent-header">--}}
{{--        <button id="header-menu-btn" class="header-menu-btn">--}}
{{--            <i class="fas fa-bars"></i>--}}
{{--        </button>--}}
{{--        <button id="search-btn" class="search-btn">--}}
{{--            <i class="fas fa-search"></i>--}}
{{--        </button>--}}
{{--    </div>--}}

    <!-- Header Menu Modal -->
    <div class="header-modal" id="header-modal">
        <div class="header-modal-content">
            <div class="header-modal-header">
                <h3>Menu</h3>
                <button class="header-modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="header-menu-items">
                <a href="/feed" class="header-menu-item">
                    <i class="fas fa-home"></i>
                    <span>Home</span>
                </a>
                <a href="/profile" class="header-menu-item">
                    <i class="fas fa-user"></i>
                    <span>Profile</span>
                </a>
                <a href="/posts/create" class="header-menu-item">
                    <i class="fas fa-video"></i>
                    <span>Video</span>
                </a>
            </div>
        </div>
    </div>

    <!-- Search Modal -->
    <div class="search-modal" id="search-modal">
        <div class="search-modal-content">
            <div class="search-modal-header">
                <h3>Search Videos</h3>
                <button class="search-modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="search-input-container">
                <input type="text" id="search-input" placeholder="Search for videos..." autocomplete="off">
                <i class="fas fa-search search-icon"></i>
            </div>
            <div class="search-results" id="search-results">
                <div class="search-placeholder">Type at least 3 characters to search...</div>
            </div>
        </div>
    </div>

    <!-- Profile Modal -->
    <div class="profile-modal" id="profile-modal">
        <div class="profile-modal-content">
            <div class="profile-modal-header">
                <h3>Profile</h3>
                <button class="profile-modal-close">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="profile-info">
                <div class="profile-avatar">
                    <div class="profile-avatar-initials" id="profile-modal-avatar"></div>
                </div>
                <div class="profile-details">
                    <h3 id="profile-modal-name"></h3>
                    <p id="profile-modal-username"></p>
                </div>
            </div>
            <div class="profile-actions">
                <button class="profile-action-btn view-profile-btn" id="view-profile-btn">
                    <i class="fas fa-user"></i>
                    <span>View Profile</span>
                </button>
                <button class="profile-action-btn share-profile-btn" id="share-profile-btn">
                    <i class="fas fa-share-alt"></i>
                    <span>Share Profile</span>
                </button>
            </div>
        </div>
    </div>

    <div class="reels-container">
        <div class="video-feed">
            @if($videos && $videos->count() > 0)
                @foreach($videos as $video)
                    <div class="video-item" data-video-id="{{ $video->id }}">
                        <div class="video-wrapper">
                            <video 
                                src="{{ $video->video_url }}"
                                loop
                                muted
                                playsinline
                                preload="metadata"
                                class="video-player"
                                webkit-playsinline
                            ></video>
                            
                            <div class="video-overlay">
                                <div class="video-info">
                                    <div class="user-info clickable" data-user-id="{{ $video->user->id }}" data-user-username="{{ $video->user->username ?? strtolower(str_replace(' ', '', $video->user->name)) }}">
                                        <div class="user-avatar-initials" style="background: {{ ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7'][rand(0,4)] }};">
                                            {{ strtoupper(substr($video->user->name, 0, 1)) }}{{ strtoupper(substr(explode(' ', $video->user->name)[1] ?? '', 0, 1)) }}
                                        </div>
                                        <div class="user-details">
                                            <h4>{{ $video->user->name }}</h4>
                                            <p>{{ '@' . ($video->user->username ?? strtolower(str_replace(' ', '', $video->user->name))) }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="video-caption">
                                        <p class="video-title">{{ $video->title }}</p>
                                        @if($video->description)
                                            <p class="video-description">{{ $video->description }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="video-actions">
                                    <div class="action-item">
                                        <button class="action-btn like-btn @if($video->is_liked) liked @endif" data-video-id="{{ $video->id }}">
                                            <i class="@if($video->is_liked) fas @else far @endif fa-heart"></i>
                                        </button>
                                        <span class="action-count">{{ $video->likes_count }}</span>
                                    </div>
                                    
                                    <div class="action-item">
                                        <button class="action-btn comment-btn" data-video-id="{{ $video->id }}">
                                            <i class="fas fa-comment"></i>
                                        </button>
                                        <span class="action-count">{{ $video->comments_count }}</span>
                                    </div>
                                    
                                    <div class="action-item">
                                        <button class="action-btn share-btn" data-video-id="{{ $video->id }}">
                                            <i class="fas fa-share"></i>
                                        </button>
                                        <span class="action-count">{{ $video->shares_count }}</span>
                                    </div>
                                    
                                    <div class="action-item">
                                        <button class="action-btn repost-btn @if($video->is_reposted) reposted @endif" data-video-id="{{ $video->id }}">
                                            <i class="fas fa-retweet"></i>
                                        </button>
                                        <span class="action-count">{{ $video->reposts_count }}</span>
                                    </div>
                                    
                                    <div class="action-item">
                                        <button class="action-btn upload-btn">
                                            <i class="fas fa-plus"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="progress-bar">
                                <div class="progress"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <div class="no-videos">
                    <i class="fas fa-video"></i>
                    <h3>No videos available</h3>
                    <p>Be the first to share a video!</p>
                    <a href="/videos/create" class="upload-first-video-btn">
                        <i class="fas fa-plus"></i>
                        Upload Your First Video
                    </a>
                </div>
            @endif
        </div>

        <!-- Comments Sidebar -->
        <div class="comments-sidebar" id="comments-sidebar">
            <div class="comments-header">
                <h3>Comments</h3>
                <button class="close-comments">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <div class="comments-list" id="comments-list">
                <div class="no-comments">
                    <p>No comments yet. Be the first to comment!</p>
                </div>
            </div>
            
            <div class="comment-form">
                <input type="text" id="comment-input" placeholder="Add a comment..." maxlength="500">
                <button id="post-comment">Post</button>
            </div>
        </div>
    </div>

    <!-- Bottom Navigation Bar -->
    <nav class="bottom-nav">
        <a href="/feed" class="bottom-nav-item active">
            <i class="fas fa-home bottom-nav-icon"></i>
            <span class="bottom-nav-text">Home</span>
        </a>

        <a href="/posts/create" class="bottom-nav-item">
            <i class="fas fa-video bottom-nav-icon"></i>
            <span class="bottom-nav-text">Video</span>
        </a>
        <a href="{{ route('cryptocurrency.wallet') }}" class="bottom-nav-item">
            <i class="fas fa-wallet bottom-nav-icon"></i>
            <span class="bottom-nav-text">Wallet</span>
        </a>
        <a href="{{ route('cryptocurrency.marketplace') }}" class="bottom-nav-item">
            <i class="fas fa-store bottom-nav-icon"></i>
            <span class="bottom-nav-text">Marketplace</span>
        </a>

        @auth
        <a href="{{ route('profile', ['username' => Auth::user()->username ?? Auth::user()->id]) }}" class="bottom-nav-item">
            <i class="fas fa-user bottom-nav-icon"></i>
            <span class="bottom-nav-text">Profile</span>
        </a>
        @else
        <a href="{{ route('login') }}" class="bottom-nav-item">
            <i class="fas fa-user bottom-nav-icon"></i>
            <span class="bottom-nav-text">Profile</span>
        </a>
        @endauth
    </nav>

    <!-- Enhanced Reels Styles -->
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            overflow: hidden;
            background-color: #000;
            user-select: none;
            -webkit-user-select: none;
            -webkit-touch-callout: none;
        }
        
        /* Video container padding for bottom navigation */
        .video-container,
        .reels-container {
            padding-bottom: 80px;
        }
        
        /* Ensure full height for reels */
        html, body {
            height: 100vh !important;
            height: 100dvh !important;
            overflow: hidden !important;
        }
        
        /* Transparent Header Styles */
        .transparent-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1002;
            padding: 20px;
            background: transparent;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .header-menu-btn,
        .search-btn {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .header-menu-btn:hover,
        .search-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.05);
        }
        
        /* Bottom Modal Styles */
        .header-modal,
        .search-modal,
        .profile-modal {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 1003;
            background: rgba(0, 0, 0, 0.5);
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
        }
        
        .header-modal.active,
        .search-modal.active,
        .profile-modal.active {
            opacity: 1;
            visibility: visible;
        }
        
        .header-modal-content,
        .search-modal-content,
        .profile-modal-content {
            background: #fff;
            border-radius: 20px 20px 0 0;
            padding: 0;
            width: 100%;
            max-height: 70vh;
            overflow: hidden;
            transform: translateY(100%);
            transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .header-modal.active .header-modal-content,
        .search-modal.active .search-modal-content,
        .profile-modal.active .profile-modal-content {
            transform: translateY(0);
        }
        
        .header-modal-header,
        .search-modal-header,
        .profile-modal-header {
            padding: 20px 20px 16px 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            position: relative;
        }
        
        /* Add drag handle to bottom modals */
        .header-modal-header::before,
        .search-modal-header::before,
        .profile-modal-header::before {
            content: '';
            position: absolute;
            top: 8px;
            left: 50%;
            transform: translateX(-50%);
            width: 40px;
            height: 4px;
            background: #ddd;
            border-radius: 2px;
        }
        
        .header-modal-header h3,
        .search-modal-header h3,
        .profile-modal-header h3 {
            margin: 0;
            color: #333;
            font-size: 18px;
            font-weight: 600;
        }
        
        .header-modal-close,
        .search-modal-close,
        .profile-modal-close {
            background: rgba(0, 0, 0, 0.05);
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #666;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .header-modal-close:hover,
        .search-modal-close:hover,
        .profile-modal-close:hover {
            background: rgba(0, 0, 0, 0.1);
            color: #333;
            transform: scale(1.05);
        }
        
        /* Header Menu Items */
        .header-menu-items {
            padding: 20px;
            background: #fff;
        }
        
        .header-menu-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 16px 20px;
            text-decoration: none;
            color: #333;
            border-radius: 12px;
            transition: all 0.3s ease;
            margin-bottom: 12px;
            font-size: 16px;
            font-weight: 500;
            border: 1px solid #f0f0f0;
        }
        
        .header-menu-item:hover {
            background: linear-gradient(135deg, #ff6b6b, #ff8a80);
            color: #fff;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        }
        
        .header-menu-item i {
            font-size: 20px;
            width: 24px;
            text-align: center;
        }
        
        
        /* Search Modal Styles */
        .search-input-container {
            position: relative;
            padding: 20px;
            background: #fff;
        }
        
        .search-input-container input {
            width: 100%;
            padding: 16px 50px 16px 20px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .search-input-container input:focus {
            border-color: #ff6b6b;
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
            background: #fff;
        }
        
        .search-icon {
            position: absolute;
            right: 35px;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
            font-size: 18px;
        }
        
        .search-results {
            max-height: 300px;
            overflow-y: auto;
            padding: 0 20px 20px;
            background: #fff;
        }
        
        .search-loading,
        .no-search-results,
        .search-placeholder {
            text-align: center;
            padding: 40px 20px;
            color: #666;
            font-size: 14px;
        }
        
        .search-result-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 15px;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            margin-bottom: 10px;
            border: 1px solid #f0f0f0;
        }
        
        .search-result-item:hover {
            background: #f8f9fa;
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }
        
        .search-result-info {
            flex: 1;
        }
        
        .search-result-info h4 {
            margin: 0 0 5px 0;
            font-size: 14px;
            font-weight: 600;
            color: #333;
            line-height: 1.3;
        }
        
        .search-result-info p {
            margin: 0;
            font-size: 13px;
            color: #666;
        }
        
        /* Profile Modal Styles */
        .profile-info {
            padding: 30px 20px 20px;
            background: #fff;
            text-align: center;
        }
        
        .profile-avatar {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        
        .profile-avatar-initials {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            border: 3px solid #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 24px;
            color: white;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .profile-details h3 {
            margin: 0 0 8px 0;
            font-size: 22px;
            font-weight: 600;
            color: #333;
        }
        
        .profile-details p {
            margin: 0;
            font-size: 16px;
            color: #666;
        }
        
        .profile-actions {
            padding: 20px;
            background: #fff;
            display: flex;
            gap: 12px;
        }
        
        .profile-action-btn {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            padding: 16px 20px;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
        }
        
        .view-profile-btn {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: #fff;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }
        
        .view-profile-btn:hover {
            background: linear-gradient(135deg, #5a6fd8, #6a4190);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(102, 126, 234, 0.4);
        }
        
        .share-profile-btn {
            background: linear-gradient(135deg, #ff6b6b, #ff8a80);
            color: #fff;
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        }
        
        .share-profile-btn:hover {
            background: linear-gradient(135deg, #ff5252, #ff6b6b);
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(255, 82, 82, 0.4);
        }
        
        .profile-action-btn i {
            font-size: 18px;
        }
        
        /* Notification Styles */
        .notification,
        .share-notification {
            position: fixed;
            top: 100px;
            left: 50%;
            transform: translateX(-50%) translateY(-20px);
            background: rgba(0, 0, 0, 0.9);
            color: #fff;
            padding: 12px 24px;
            border-radius: 25px;
            font-size: 14px;
            font-weight: 500;
            z-index: 2000;
            opacity: 0;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
        }
        
        .notification.show,
        .share-notification.show {
            opacity: 1;
            transform: translateX(-50%) translateY(0);
        }
        
        /* Comments Close Button in Header */
        .comments-close-header {
            position: fixed;
            top: 20px;
            right: 20px;
            z-index: 1004;
            background: rgba(0, 0, 0, 0.7);
            border: none;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: none;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .comments-close-header.active {
            display: flex;
        }
        
        .comments-close-header:hover {
            background: rgba(0, 0, 0, 0.9);
            transform: scale(1.05);
        }
        
        .reels-container {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            height: 100dvh;
            background: #000;
            z-index: 1000;
        }
        
        .video-feed {
            width: 100%;
            height: 100vh;
            height: 100dvh;
            overflow-y: scroll;
            scroll-snap-type: y mandatory;
            scrollbar-width: none;
            -ms-overflow-style: none;
        }
        
        .video-feed::-webkit-scrollbar {
            display: none;
        }
        
        .video-item {
            width: 100%;
            height: 100vh;
            height: 100dvh;
            position: relative;
            scroll-snap-align: start;
            scroll-snap-stop: always;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        
        .video-wrapper {
            width: 100%;
            height: 100%;
            position: relative;
            overflow: hidden;
        }
        
        .video-player {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            cursor: pointer;
            background: #000;
        }
        
        .sound-control-btn {
            position: absolute;
            top: 80px;
            right: 20px;
            background: rgba(0, 0, 0, 0.5);
            border: none;
            color: #fff;
            font-size: 18px;
            cursor: pointer;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 15;
            transition: all 0.3s ease;
        }
        
        .sound-control-btn:hover {
            background: rgba(0, 0, 0, 0.8);
            transform: scale(1.1);
        }
        
        .video-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 20px;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.8));
            color: #fff;
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            z-index: 10;
        }
        
        .video-info {
            flex: 0 0 auto;
            max-width: calc(100% - 90px);
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: flex-start;
        }
        
        .video-actions {
            flex: 0 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: flex-end;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            margin-bottom: 15px;
            gap: 12px;
            width: 100%;
        }
        
        .user-details {
            text-align: left;
        }
        
        .user-details h4,
        .user-details p {
            text-align: left;
        }
        
        /* Make user info clickable */
        .user-info.clickable {
            cursor: pointer;
            transition: all 0.3s ease;
            padding: 8px;
            border-radius: 12px;
            margin: -8px -8px 7px -8px;
        }
        
        .user-info.clickable:hover {
            background: rgba(255, 255, 255, 0.1);
            transform: scale(1.02);
        }
        
        .user-info.clickable:active {
            transform: scale(0.98);
        }
        
        .user-avatar-initials {
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 2px solid #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 16px;
            color: white;
            flex-shrink: 0;
        }
        
        .user-details h4 {
            margin: 0 0 4px 0;
            font-size: 16px;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }
        
        .user-details p {
            margin: 0;
            font-size: 14px;
            opacity: 0.8;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }
        
        .video-caption {
            margin-top: 8px;
        }
        
        .video-title {
            margin: 0 0 8px 0;
            font-size: 16px;
            font-weight: 600;
            line-height: 1.3;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
        }
        
        .video-description {
            font-size: 14px;
            opacity: 0.9;
            line-height: 1.4;
            margin: 0;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        
        .video-actions {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 15px;
            padding-bottom: 100px;
        }
        
        .action-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 4px;
        }
        
        .action-btn {
            background: rgba(255, 255, 255, 0.15);
            border: none;
            color: #fff;
            font-size: 20px;
            cursor: pointer;
            width: 52px;
            height: 52px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s ease;
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .action-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            transform: scale(1.05);
        }
        
        .action-btn:active {
            transform: scale(0.95);
        }
        
        .like-btn.liked {
            background: rgba(255, 23, 68, 0.3);
            color: #ff1744;
            border-color: #ff1744;
        }
        
        .repost-btn.reposted {
            background: rgba(23, 191, 99, 0.3);
            color: #17bf63;
            border-color: #17bf63;
        }
        
        .upload-btn {
            background: rgba(0, 123, 255, 0.2);
            color: #007bff;
            border-color: #007bff;
        }
        
        .action-count {
            color: #fff;
            font-size: 12px;
            font-weight: 600;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.7);
            min-height: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 4px;
        }
        
        .progress-bar {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: rgba(255, 255, 255, 0.3);
            z-index: 15;
        }
        
        .progress {
            height: 100%;
            background: linear-gradient(90deg, #ff6b6b, #ff8a80);
            width: 0%;
            transition: width 0.1s linear;
        }
        
        /* Comments Sidebar */
        .comments-sidebar {
            position: fixed;
            top: 0;
            right: -25%;
            width: 25%;
            height: 100vh;
            background: #fff;
            transition: right 0.4s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1001;
            display: flex;
            flex-direction: column;
            box-shadow: -4px 0 20px rgba(0, 0, 0, 0.15);
            min-width: 320px;
            opacity: 0;
            visibility: hidden;
            transform: translateX(100%);
        }
        
        .comments-sidebar.active {
            right: 0;
            opacity: 1;
            visibility: visible;
            transform: translateX(0);
        }
        
        .comments-header {
            padding: 20px;
            border-bottom: 1px solid #eee;
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }
        
        .comments-header h3 {
            margin: 0;
            color: #333;
            font-size: 18px;
            font-weight: 600;
        }
        
        .close-comments {
            background: rgba(0, 0, 0, 0.05);
            border: none;
            font-size: 18px;
            cursor: pointer;
            color: #666;
            padding: 8px;
            border-radius: 50%;
            transition: all 0.3s ease;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .close-comments:hover {
            background: rgba(0, 0, 0, 0.1);
            color: #333;
            transform: scale(1.05);
        }
        
        .comments-list {
            flex: 1;
            overflow-y: auto;
            padding: 20px;
        }
        
        .no-comments {
            text-align: center;
            color: #888;
            padding: 40px 20px;
        }
        
        .loading {
            text-align: center;
            color: #888;
            padding: 40px 20px;
        }
        
        .comment-item {
            display: flex;
            margin-bottom: 16px;
            gap: 12px;
            padding: 12px;
            border-radius: 12px;
            background: rgba(0, 0, 0, 0.02);
            transition: background-color 0.3s ease;
        }
        
        .comment-item:hover {
            background: rgba(0, 0, 0, 0.05);
        }
        
        .comment-avatar-initials {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 13px;
            color: white;
            flex-shrink: 0;
            border: 2px solid #e9ecef;
        }
        
        .comment-content {
            flex: 1;
        }
        
        .comment-content strong {
            color: #333;
            font-size: 14px;
            font-weight: 600;
        }
        
        .comment-content p {
            margin: 4px 0;
            color: #333;
            font-size: 14px;
            line-height: 1.4;
        }
        
        .comment-time {
            color: #666;
            font-size: 12px;
        }
        
        .comment-form {
            padding: 20px;
            border-top: 1px solid #eee;
            display: flex;
            gap: 12px;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        }
        
        .comment-form input {
            flex: 1;
            padding: 12px 18px;
            border: 2px solid #e9ecef;
            border-radius: 25px;
            font-size: 14px;
            outline: none;
            transition: all 0.3s ease;
            background: #fff;
        }
        
        .comment-form input:focus {
            border-color: #ff6b6b;
            box-shadow: 0 0 0 3px rgba(255, 107, 107, 0.1);
        }
        
        .comment-form button {
            background: linear-gradient(135deg, #ff6b6b, #ff8a80);
            color: #fff;
            border: none;
            border-radius: 25px;
            padding: 12px 24px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 12px rgba(255, 107, 107, 0.3);
        }
        
        .comment-form button:hover:not(:disabled) {
            background: linear-gradient(135deg, #ff5252, #ff6b6b);
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(255, 82, 82, 0.4);
        }
        
        .comment-form button:disabled {
            opacity: 0.6;
            cursor: not-allowed;
            transform: none;
        }
        
        .no-videos {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            height: 100vh;
            color: #fff;
            text-align: center;
            padding: 40px;
        }
        
        .no-videos i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.5;
        }
        
        .no-videos h3 {
            margin-bottom: 10px;
            font-size: 1.5rem;
        }
        
        .no-videos p {
            opacity: 0.7;
            margin-bottom: 30px;
        }
        
        .upload-first-video-btn {
            display: inline-flex;
            align-items: center;
            gap: 12px;
            background: linear-gradient(135deg, #ff6b6b, #ff8a80);
            color: #fff;
            text-decoration: none;
            padding: 16px 32px;
            border-radius: 50px;
            font-size: 16px;
            font-weight: 600;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(255, 107, 107, 0.3);
        }
        
        .upload-first-video-btn:hover {
            background: linear-gradient(135deg, #ff5252, #ff6b6b);
            transform: translateY(-2px);
            box-shadow: 0 6px 25px rgba(255, 82, 82, 0.4);
            color: #fff;
        }
        
        .upload-first-video-btn i {
            font-size: 18px;
        }
        
        /* Mobile Optimizations */
        @media (max-width: 768px) {
            .transparent-header {
                padding: 15px;
            }
            
            .header-menu-btn,
            .search-btn {
                width: 44px;
                height: 44px;
                font-size: 16px;
            }
            
            .comments-close-header {
                width: 44px;
                height: 44px;
                font-size: 18px;
                top: 15px;
                right: 15px;
            }
            
            .comments-sidebar {
                width: 100%;
                right: -100%;
                min-width: unset;
            }
            
            .video-overlay {
                padding: 15px;
                justify-content: space-between !important;
                align-items: flex-end !important;
            }
            
            .video-info {
                max-width: calc(100% - 75px);
                display: flex;
                flex-direction: column;
                align-items: flex-start !important;
                justify-content: flex-start !important;
                margin: 0 !important;
                padding: 0 !important;
                flex: 0 0 auto;
            }
            
            .video-actions {
                flex: 0 0 auto;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: flex-end !important;
            }
            
            .user-info {
                display: flex !important;
                align-items: center !important;
                justify-content: flex-start !important;
                width: auto !important;
                margin-left: 0 !important;
                margin-right: auto !important;
                margin-bottom: 15px;
            }
            
            .user-avatar-initials {
                width: 40px;
                height: 40px;
                font-size: 14px;
                margin-right: 0 !important;
            }
            
            .user-details {
                text-align: left !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .user-details h4 {
                font-size: 14px;
                text-align: left !important;
                margin: 0 0 4px 0 !important;
            }
            
            .user-details p {
                font-size: 13px;
                text-align: left !important;
                margin: 0 !important;
            }
            
            .video-title {
                font-size: 15px;
            }
            
            .video-description {
                font-size: 13px;
            }
            
            .action-btn {
                width: 48px;
                height: 48px;
                font-size: 18px;
            }
            
            .video-actions {
                gap: 12px;
                padding-bottom: 100px;
            }
            
            .action-count {
                font-size: 11px;
            }
            
            .sound-control-btn {
                width: 44px;
                height: 44px;
                font-size: 16px;
                top: 70px;
                right: 15px;
            }
            
            .profile-avatar-initials {
                width: 70px;
                height: 70px;
                font-size: 20px;
            }
            
            .profile-details h3 {
                font-size: 18px;
            }
            
            .profile-action-btn {
                font-size: 13px;
                padding: 12px 14px;
            }
            
            .profile-action-btn i {
                font-size: 16px;
            }
            
            .upload-first-video-btn {
                font-size: 14px;
                padding: 14px 28px;
            }
        }
        
        /* Tablet responsive */
        @media (min-width: 769px) and (max-width: 1024px) {
            .comments-sidebar {
                width: 35%;
                right: -35%;
                min-width: 350px;
            }
        }
        
        /* Ensure full coverage on all devices */
        @media (orientation: landscape) and (max-width: 768px) {
            .transparent-header {
                padding: 10px 15px;
            }
            
            .video-overlay {
                padding: 10px 15px;
            }
            
            .video-actions {
                gap: 8px;
                padding-bottom: 90px;
            }
            
            .action-btn {
                width: 40px;
                height: 40px;
                font-size: 14px;
            }
            
            .action-count {
                font-size: 10px;
            }
            
            .video-overlay {
                justify-content: space-between !important;
                align-items: flex-end !important;
            }
            
            .video-info {
                align-items: flex-start !important;
                justify-content: flex-start !important;
                margin: 0 !important;
                padding: 0 !important;
                flex: 0 0 auto;
            }
            
            .video-actions {
                flex: 0 0 auto;
                display: flex !important;
                flex-direction: column !important;
                align-items: center !important;
                justify-content: flex-end !important;
            }
            
            .user-info {
                justify-content: flex-start !important;
                margin-left: 0 !important;
                margin-right: auto !important;
            }
            
            .user-avatar-initials {
                width: 32px;
                height: 32px;
                font-size: 12px;
            }
            
            .user-details {
                text-align: left !important;
            }
            
            .user-details h4,
            .user-details p {
                text-align: left !important;
            }
            
            .sound-control-btn {
                top: 50px;
            }
            
            .profile-modal-content {
                max-height: 80vh;
            }
            
            .profile-avatar-initials {
                width: 60px;
                height: 60px;
                font-size: 18px;
            }
            
            .profile-details h3 {
                font-size: 18px;
            }
            
            .profile-action-btn {
                font-size: 14px;
                padding: 12px 16px;
            }
        }
        
        /* Bottom Navigation Bar */
        .bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            background: rgba(0, 0, 0, 0.9);
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 10px 5px;
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1000;
        }
        
        .bottom-nav-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-decoration: none;
            color: #999;
            transition: all 0.3s ease;
            padding: 6px 8px;
            border-radius: 12px;
            flex: 1;
        }
        
        .bottom-nav-item.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }
        
        .bottom-nav-item:hover {
            color: #fff;
            transform: translateY(-2px);
        }
        
        .bottom-nav-icon {
            font-size: 18px;
            margin-bottom: 3px;
        }
        
        .bottom-nav-text {
            font-size: 11px;
            font-weight: 500;
        }
        
        
        /* Ensure bottom navigation is visible */
        .bottom-nav {
            display: flex !important;
            visibility: visible !important;
            opacity: 1 !important;
            pointer-events: auto !important;
        }
        
        /* JavaScript for Bottom Navigation */
        document.addEventListener('DOMContentLoaded', function() {
            // Handle bottom navigation active state
            const currentPath = window.location.pathname;
            const bottomNavItems = document.querySelectorAll('.bottom-nav-item');
            
            bottomNavItems.forEach(item => {
                item.classList.remove('active');
                const href = item.getAttribute('href');
                
                if (currentPath === href || (href === '/feed' && currentPath === '/')) {
                    item.classList.add('active');
                }
            });
        });
        
    </style>
@stop