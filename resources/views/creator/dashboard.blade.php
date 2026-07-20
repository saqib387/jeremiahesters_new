@extends('layouts.generic')

@section('page_title', 'Creator Dashboard')

@section('styles')
<style>
    .creator-dashboard {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .dashboard-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 30px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .dashboard-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin: 0;
    }
    
    .quick-actions {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    
    .quick-action-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #830866 0%, #a10a7f 100%);
        color: #fff;
    }

    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(131, 8, 102, 0.3);
        color: #fff;
    }
    
    .btn-secondary-custom {
        background: #f0f0f0;
        color: #333;
    }
    
    .btn-secondary-custom:hover {
        background: #e0e0e0;
    }
    
    /* Verification Alert */
    .verification-alert {
        padding: 15px 20px;
        border-radius: 12px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .verification-alert.pending {
        background: #fff3cd;
        border: 1px solid #ffc107;
    }
    
    .verification-alert.not-verified {
        background: #f8d7da;
        border: 1px solid #dc3545;
    }
    
    .verification-alert.verified {
        background: #d4edda;
        border: 1px solid #28a745;
    }
    
    .verification-alert i {
        font-size: 24px;
    }
    
    .verification-alert.pending i { color: #ffc107; }
    .verification-alert.not-verified i { color: #dc3545; }
    .verification-alert.verified i { color: #28a745; }
    
    .verification-content {
        flex: 1;
    }
    
    .verification-content h4 {
        margin: 0 0 5px 0;
        font-size: 16px;
        font-weight: 600;
    }
    
    .verification-content p {
        margin: 0;
        font-size: 14px;
        opacity: 0.8;
    }
    
    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 15px;
        font-size: 22px;
    }
    
    .stat-icon.views { background: rgba(0, 123, 255, 0.1); color: #007bff; }
    .stat-icon.likes { background: rgba(255, 0, 80, 0.1); color: #FF0050; }
    .stat-icon.comments { background: rgba(40, 167, 69, 0.1); color: #28a745; }
    .stat-icon.videos { background: rgba(108, 117, 125, 0.1); color: #6c757d; }
    .stat-icon.streams { background: rgba(156, 39, 176, 0.1); color: #9c27b0; }
    
    .stat-value {
        font-size: 32px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 14px;
        color: #666;
    }
    
    /* Section Headers */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
    }
    
    .section-header h2 {
        font-size: 20px;
        font-weight: 600;
        color: #333;
        margin: 0;
    }
    
    .section-header a {
        color: #830866;
        text-decoration: none;
        font-weight: 500;
        font-size: 14px;
    }
    
    .section-header a:hover {
        text-decoration: underline;
    }
    
    /* Content Grid */
    .content-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .content-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }
    
    .content-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .content-thumbnail {
        position: relative;
        aspect-ratio: 16/9;
        background: #f0f0f0;
        overflow: hidden;
    }
    
    .content-thumbnail video,
    .content-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .content-overlay {
        position: absolute;
        bottom: 8px;
        left: 8px;
        display: flex;
        gap: 8px;
    }
    
    .content-badge {
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .content-info {
        padding: 15px;
    }
    
    .content-title {
        font-size: 15px;
        font-weight: 600;
        color: #333;
        margin: 0 0 8px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .content-stats {
        display: flex;
        gap: 15px;
        font-size: 13px;
        color: #666;
    }
    
    .content-stats span {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .content-date {
        font-size: 12px;
        color: #999;
        margin-top: 8px;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #f8f9fa;
        border-radius: 16px;
        margin-bottom: 30px;
    }
    
    .empty-state i {
        font-size: 48px;
        color: #ccc;
        margin-bottom: 20px;
    }
    
    .empty-state h3 {
        font-size: 18px;
        color: #666;
        margin-bottom: 10px;
    }
    
    .empty-state p {
        color: #999;
        margin-bottom: 20px;
    }
    
    /* Creator Menu Navigation */
    .creator-nav {
        display: flex;
        gap: 5px;
        background: #f8f9fa;
        padding: 5px;
        border-radius: 12px;
        margin-bottom: 25px;
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        scrollbar-width: thin;
        scrollbar-color: rgba(131, 8, 102, 0.35) transparent;
    }

    .creator-nav::-webkit-scrollbar {
        height: 4px;
    }
    .creator-nav::-webkit-scrollbar-track {
        background: transparent;
    }
    .creator-nav::-webkit-scrollbar-thumb {
        background: rgba(131, 8, 102, 0.3);
        border-radius: 10px;
    }
    
    .creator-nav a {
        padding: 12px 20px;
        border-radius: 8px;
        text-decoration: none;
        color: #666;
        font-weight: 500;
        font-size: 14px;
        white-space: nowrap;
        transition: all 0.3s ease;
    }
    
    .creator-nav a.active {
        background: #fff;
        color: #830866;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .creator-nav a:hover:not(.active) {
        background: rgba(255, 255, 255, 0.5);
        color: #333;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .creator-dashboard {
            padding: 15px;
        }
        
        .dashboard-header {
            flex-direction: column;
            align-items: flex-start;
        }
        
        .dashboard-header h1 {
            font-size: 24px;
        }
        
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .stat-card {
            padding: 15px;
        }
        
        .stat-value {
            font-size: 24px;
        }
        
        .content-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .creator-nav {
            padding: 4px;
        }
        
        .creator-nav a {
            padding: 10px 15px;
            font-size: 13px;
        }
    }
    
    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .content-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="creator-dashboard">
    <!-- Dashboard Header -->
    <div class="dashboard-header">
        <h1><i class="fas fa-chart-line me-2"></i> Creator Dashboard</h1>
        <div class="quick-actions">
            <a href="/create" class="quick-action-btn btn-primary-custom">
                <i class="fas fa-video"></i> Upload Video
            </a>
            @if($isVerified)
                <a href="{{ route('streams.create') }}" class="quick-action-btn btn-secondary-custom">
                    <i class="fas fa-broadcast-tower"></i> Go Live
                </a>
            @endif
        </div>
    </div>
    
    <!-- Creator Navigation -->
    <nav class="creator-nav">
        <a href="{{ route('creator.dashboard') }}" class="active">
            <i class="fas fa-home me-1"></i> Overview
        </a>
        <a href="{{ route('creator.videos') }}">
            <i class="fas fa-video me-1"></i> My Videos
        </a>
        <a href="{{ route('creator.streams') }}">
            <i class="fas fa-broadcast-tower me-1"></i> Livestreams
        </a>
        <a href="{{ route('creator.analytics') }}">
            <i class="fas fa-chart-bar me-1"></i> Analytics
        </a>
        <a href="{{ route('my.settings', ['type' => 'profile']) }}">
            <i class="fas fa-cog me-1"></i> Settings
        </a>
    </nav>
    
    <!-- Verification Status Alert -->
    @if($verificationStatus === 'not_submitted')
        <div class="verification-alert not-verified">
            <i class="fas fa-exclamation-circle"></i>
            <div class="verification-content">
                <h4>Verify Your Identity</h4>
                <p>Complete ID verification to unlock livestreaming and earn more trust from your audience.</p>
            </div>
            <a href="{{ route('my.settings', ['type' => 'verify']) }}" class="quick-action-btn btn-primary-custom">
                Verify Now
            </a>
        </div>
    @elseif($verificationStatus === 'pending')
        <div class="verification-alert pending">
            <i class="fas fa-clock"></i>
            <div class="verification-content">
                <h4>Verification Pending</h4>
                <p>Your ID verification is being reviewed. This usually takes 1-2 business days.</p>
            </div>
        </div>
    @elseif($verificationStatus === 'rejected')
        <div class="verification-alert not-verified">
            <i class="fas fa-times-circle"></i>
            <div class="verification-content">
                <h4>Verification Rejected</h4>
                <p>Your verification was not approved. Please try again with clear, valid documents.</p>
            </div>
            <a href="{{ route('my.settings', ['type' => 'verify']) }}" class="quick-action-btn btn-primary-custom">
                Try Again
            </a>
        </div>
    @elseif($isVerified)
        <div class="verification-alert verified">
            <i class="fas fa-check-circle"></i>
            <div class="verification-content">
                <h4>Verified Creator</h4>
                <p>Your identity is verified. You have access to all creator features including livestreaming.</p>
            </div>
        </div>
    @endif
    
    <!-- Stats Overview -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon views">
                <i class="fas fa-eye"></i>
            </div>
            <div class="stat-value">{{ number_format($totalViews) }}</div>
            <div class="stat-label">Total Views</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon likes">
                <i class="fas fa-heart"></i>
            </div>
            <div class="stat-value">{{ number_format($totalLikes) }}</div>
            <div class="stat-label">Total Likes</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon comments">
                <i class="fas fa-comment"></i>
            </div>
            <div class="stat-value">{{ number_format($totalComments) }}</div>
            <div class="stat-label">Total Comments</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon videos">
                <i class="fas fa-video"></i>
            </div>
            <div class="stat-value">{{ number_format($totalVideos) }}</div>
            <div class="stat-label">Videos Uploaded</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon streams">
                <i class="fas fa-broadcast-tower"></i>
            </div>
            <div class="stat-value">{{ number_format($totalStreams) }}</div>
            <div class="stat-label">Livestreams</div>
        </div>
    </div>
    
    <!-- Recent Videos -->
    <div class="section-header">
        <h2><i class="fas fa-video me-2"></i> Recent Videos</h2>
        <a href="{{ route('creator.videos') }}">View All <i class="fas fa-arrow-right ms-1"></i></a>
    </div>
    
    @if($videos->count() > 0)
        <div class="content-grid">
            @foreach($videos->take(4) as $video)
                <div class="content-card">
                    <div class="content-thumbnail">
                        @if($video->thumbnail_url)
                            <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}">
                        @else
                            <video src="{{ $video->video_url }}" muted></video>
                        @endif
                        <div class="content-overlay">
                            <span class="content-badge">
                                <i class="fas fa-eye"></i> {{ number_format($video->views_count ?? 0) }}
                            </span>
                        </div>
                    </div>
                    <div class="content-info">
                        <h3 class="content-title">{{ $video->title }}</h3>
                        <div class="content-stats">
                            <span><i class="fas fa-heart"></i> {{ number_format($video->likes_count ?? 0) }}</span>
                            <span><i class="fas fa-comment"></i> {{ number_format($video->comments_count ?? 0) }}</span>
                        </div>
                        <div class="content-date">
                            {{ $video->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-video"></i>
            <h3>No videos yet</h3>
            <p>Start creating content to grow your audience!</p>
            <a href="/create" class="quick-action-btn btn-primary-custom">
                <i class="fas fa-plus me-1"></i> Upload Your First Video
            </a>
        </div>
    @endif
    
    <!-- Recent Streams -->
    <div class="section-header">
        <h2><i class="fas fa-broadcast-tower me-2"></i> Recent Livestreams</h2>
        <a href="{{ route('creator.streams') }}">View All <i class="fas fa-arrow-right ms-1"></i></a>
    </div>
    
    @if($streams->count() > 0)
        <div class="content-grid">
            @foreach($streams->take(4) as $stream)
                <div class="content-card">
                    <div class="content-thumbnail">
                        @if($stream->thumbnail)
                            <img src="{{ $stream->thumbnail_url }}" alt="{{ $stream->title }}">
                        @else
                            <div style="width: 100%; height: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-broadcast-tower" style="font-size: 48px; color: rgba(255,255,255,0.3);"></i>
                            </div>
                        @endif
                        <div class="content-overlay">
                            @if($stream->is_live)
                                <span class="content-badge" style="background: #dc3545;">
                                    <i class="fas fa-circle"></i> LIVE
                                </span>
                            @endif
                            <span class="content-badge">
                                <i class="fas fa-users"></i> {{ number_format($stream->viewer_count ?? 0) }}
                            </span>
                        </div>
                    </div>
                    <div class="content-info">
                        <h3 class="content-title">{{ $stream->title }}</h3>
                        <div class="content-stats">
                            <span><i class="fas fa-clock"></i> {{ $stream->formatted_duration }}</span>
                            <span><i class="fas fa-users"></i> {{ number_format($stream->peak_viewer_count ?? 0) }} peak</span>
                        </div>
                        <div class="content-date">
                            {{ $stream->created_at->diffForHumans() }}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-broadcast-tower"></i>
            <h3>No livestreams yet</h3>
            @if($isVerified)
                <p>Go live and connect with your audience in real-time!</p>
                <a href="{{ route('streams.create') }}" class="quick-action-btn btn-primary-custom">
                    <i class="fas fa-broadcast-tower me-1"></i> Start Your First Stream
                </a>
            @else
                <p>Verify your identity to start livestreaming.</p>
                <a href="{{ route('my.settings', ['type' => 'verify']) }}" class="quick-action-btn btn-primary-custom">
                    <i class="fas fa-check-circle me-1"></i> Verify Identity
                </a>
            @endif
        </div>
    @endif
</div>
@endsection
