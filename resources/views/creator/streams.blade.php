@extends('layouts.generic')

@section('page_title', 'My Livestreams - Creator Dashboard')

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
        background: linear-gradient(135deg, #FF0050 0%, #FF3366 100%);
        color: #fff;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        text-decoration: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(255, 0, 80, 0.3);
        color: #fff;
    }
    
    .btn-primary-custom.disabled {
        opacity: 0.5;
        cursor: not-allowed;
        pointer-events: none;
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
        color: #FF0050;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    }
    
    .creator-nav a:hover:not(.active) {
        background: rgba(255, 255, 255, 0.5);
        color: #333;
    }
    
    .verification-banner {
        background: linear-gradient(135deg, #fff3cd, #ffeeba);
        border: 1px solid #ffc107;
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
        display: flex;
        align-items: center;
        gap: 15px;
    }
    
    .verification-banner i {
        font-size: 32px;
        color: #ffc107;
    }
    
    .verification-banner .content {
        flex: 1;
    }
    
    .verification-banner h4 {
        margin: 0 0 5px 0;
        font-size: 16px;
        font-weight: 600;
    }
    
    .verification-banner p {
        margin: 0;
        font-size: 14px;
        color: #666;
    }
    
    .streams-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 20px;
    }
    
    .stream-card {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #f0f0f0;
        transition: all 0.3s ease;
    }
    
    .stream-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    
    .stream-thumbnail {
        position: relative;
        aspect-ratio: 16/9;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .stream-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .stream-thumbnail i {
        font-size: 48px;
        color: rgba(255, 255, 255, 0.3);
    }
    
    .live-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        background: #dc3545;
        color: #fff;
        padding: 4px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    
    .live-badge i {
        font-size: 8px;
        animation: pulse 1s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }
    
    .stream-info {
        padding: 15px;
    }
    
    .stream-title {
        font-size: 16px;
        font-weight: 600;
        color: #333;
        margin: 0 0 10px 0;
    }
    
    .stream-stats {
        display: flex;
        gap: 15px;
        font-size: 13px;
        color: #666;
        margin-bottom: 10px;
    }
    
    .stream-stats span {
        display: flex;
        align-items: center;
        gap: 4px;
    }
    
    .stream-status {
        display: inline-block;
        padding: 4px 10px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 500;
    }
    
    .stream-status.live { background: #dc3545; color: #fff; }
    .stream-status.ended { background: #6c757d; color: #fff; }
    .stream-status.pending { background: #ffc107; color: #333; }
    
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
</style>
@endsection

@section('content')
<div class="creator-page">
    <div class="page-header">
        <h1><i class="fas fa-broadcast-tower me-2"></i> My Livestreams</h1>
        @if($canStream)
            <a href="{{ route('streams.create') }}" class="btn-primary-custom">
                <i class="fas fa-plus"></i> Go Live
            </a>
        @else
            <a href="{{ route('my.settings', ['type' => 'verify']) }}" class="btn-primary-custom">
                <i class="fas fa-check-circle"></i> Verify to Stream
            </a>
        @endif
    </div>
    
    <!-- Creator Navigation -->
    <nav class="creator-nav">
        <a href="{{ route('creator.dashboard') }}">
            <i class="fas fa-home me-1"></i> Overview
        </a>
        <a href="{{ route('creator.videos') }}">
            <i class="fas fa-video me-1"></i> My Videos
        </a>
        <a href="{{ route('creator.streams') }}" class="active">
            <i class="fas fa-broadcast-tower me-1"></i> Livestreams
        </a>
        <a href="{{ route('creator.analytics') }}">
            <i class="fas fa-chart-bar me-1"></i> Analytics
        </a>
        <a href="{{ route('my.settings', ['type' => 'profile']) }}">
            <i class="fas fa-cog me-1"></i> Settings
        </a>
    </nav>
    
    @if(!$canStream)
        <div class="verification-banner">
            <i class="fas fa-exclamation-triangle"></i>
            <div class="content">
                <h4>ID Verification Required</h4>
                <p>You need to verify your identity before you can start livestreaming. This helps keep our community safe and builds trust with your viewers.</p>
            </div>
            <a href="{{ route('my.settings', ['type' => 'verify']) }}" class="btn-primary-custom">
                Verify Now
            </a>
        </div>
    @endif
    
    @if($streams->count() > 0)
        <div class="streams-grid">
            @foreach($streams as $stream)
                <div class="stream-card">
                    <div class="stream-thumbnail">
                        @if($stream->thumbnail)
                            <img src="{{ $stream->thumbnail_url }}" alt="{{ $stream->title }}">
                        @else
                            <i class="fas fa-broadcast-tower"></i>
                        @endif
                        @if($stream->is_live)
                            <div class="live-badge">
                                <i class="fas fa-circle"></i> LIVE
                            </div>
                        @endif
                    </div>
                    <div class="stream-info">
                        <h3 class="stream-title">{{ $stream->title }}</h3>
                        <div class="stream-stats">
                            <span><i class="fas fa-users"></i> {{ number_format($stream->viewer_count ?? 0) }} viewers</span>
                            <span><i class="fas fa-clock"></i> {{ $stream->formatted_duration }}</span>
                        </div>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="stream-status {{ $stream->status }}">
                                {{ ucfirst($stream->status) }}
                            </span>
                            <small class="text-muted">{{ $stream->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="mt-4">
            {{ $streams->links() }}
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-broadcast-tower"></i>
            <h3>No livestreams yet</h3>
            @if($canStream)
                <p>Go live and connect with your audience in real-time!</p>
                <a href="{{ route('streams.create') }}" class="btn-primary-custom">
                    <i class="fas fa-plus me-1"></i> Start Your First Stream
                </a>
            @else
                <p>Verify your identity to unlock livestreaming.</p>
                <a href="{{ route('my.settings', ['type' => 'verify']) }}" class="btn-primary-custom">
                    <i class="fas fa-check-circle me-1"></i> Verify Identity
                </a>
            @endif
        </div>
    @endif
</div>
@endsection
