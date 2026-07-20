@extends('layouts.generic')

@section('page_title', 'My Videos - Creator Dashboard')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .creator-page {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .page-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        flex-wrap: wrap;
        gap: 15px;
    }
    
    .page-header h1 {
        font-size: 24px;
        font-weight: 700;
        color: #333;
        margin: 0;
    }
    
    .btn-primary-custom {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 10px 20px;
        background: linear-gradient(135deg, #830866 0%, #a10a7f 100%);
        color: #fff;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(131, 8, 102, 0.3);
        color: #fff;
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
    
    .videos-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 20px;
    }
    
    .video-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }
    
    .video-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .video-thumbnail {
        position: relative;
        aspect-ratio: 16/9;
        background: #f0f0f0;
    }
    
    .video-thumbnail video,
    .video-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .video-overlay {
        position: absolute;
        bottom: 8px;
        left: 8px;
        right: 8px;
        display: flex;
        justify-content: space-between;
    }
    
    .video-badge {
        background: rgba(0, 0, 0, 0.7);
        color: #fff;
        padding: 4px 8px;
        border-radius: 4px;
        font-size: 12px;
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .video-info {
        padding: 15px;
    }
    
    .video-title {
        font-size: 15px;
        font-weight: 600;
        color: #333;
        margin: 0 0 10px 0;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .video-stats {
        display: flex;
        gap: 15px;
        font-size: 13px;
        color: #666;
        margin-bottom: 10px;
    }
    
    .video-stats span {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .video-actions {
        display: flex;
        gap: 10px;
    }
    
    .video-action-btn {
        flex: 1;
        padding: 8px;
        border: 1px solid #e0e0e0;
        border-radius: 6px;
        background: #fff;
        color: #666;
        font-size: 13px;
        cursor: pointer;
        transition: all 0.2s ease;
        text-decoration: none;
        text-align: center;
    }
    
    .video-action-btn:hover {
        background: #f0f0f0;
        color: #333;
    }
    
    .video-action-btn.delete:hover {
        background: #dc3545;
        border-color: #dc3545;
        color: #fff;
    }
    
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: #f8f9fa;
        border-radius: 16px;
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
    
    @media (max-width: 768px) {
        .videos-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
    
    @media (max-width: 480px) {
        .videos-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="creator-page">
    <div class="page-header">
        <h1><i class="fas fa-video me-2"></i> My Videos</h1>
        <a href="/create" class="btn-primary-custom">
            <i class="fas fa-plus"></i> Upload New Video
        </a>
    </div>
    
    <!-- Creator Navigation -->
    <nav class="creator-nav">
        <a href="{{ route('creator.dashboard') }}">
            <i class="fas fa-home me-1"></i> Overview
        </a>
        <a href="{{ route('creator.videos') }}" class="active">
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
    
    @if($videos->count() > 0)
        <div class="videos-grid">
            @foreach($videos as $video)
                <div class="video-card">
                    <div class="video-thumbnail">
                        @if($video->thumbnail_url)
                            <img src="{{ $video->thumbnail_url }}" alt="{{ $video->title }}">
                        @else
                            <video src="{{ $video->video_url }}" muted></video>
                        @endif
                        <div class="video-overlay">
                            <span class="video-badge">
                                <i class="fas fa-eye"></i> {{ number_format($video->views_count ?? 0) }}
                            </span>
                            <span class="video-badge">
                                {{ $video->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>
                    <div class="video-info">
                        <h3 class="video-title">{{ $video->title }}</h3>
                        <div class="video-stats">
                            <span><i class="fas fa-heart"></i> {{ number_format($video->likes_count ?? 0) }}</span>
                            <span><i class="fas fa-comment"></i> {{ number_format($video->comments_count ?? 0) }}</span>
                            <span><i class="fas fa-share"></i> {{ number_format($video->shares_count ?? 0) }}</span>
                        </div>
                        <div class="video-actions">
                            <a href="{{ route('videos.edit', $video) }}" class="video-action-btn">
                                <i class="fas fa-edit"></i> Edit
                            </a>
                            <a href="{{ route('videos.show', $video) }}" class="video-action-btn">
                                <i class="fas fa-eye"></i> View
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4">
            {{ $videos->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-video"></i>
            <h3>No videos yet</h3>
            <p>Start creating content to grow your audience!</p>
            <a href="/create" class="btn-primary-custom">
                <i class="fas fa-plus me-1"></i> Upload Your First Video
            </a>
        </div>
    @endif
</div>
@endsection
