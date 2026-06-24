@extends('layouts.generic')

@section('page_title', 'Analytics - Creator Dashboard')

@section('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<style>
    .creator-page {
        padding: 20px;
        max-width: 1400px;
        margin: 0 auto;
    }
    
    .page-header {
        margin-bottom: 25px;
    }
    
    .page-header h1 {
        font-size: 24px;
        font-weight: 700;
        color: #333;
        margin: 0;
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
    
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }
    
    .stat-card {
        background: #fff;
        border-radius: 16px;
        padding: 25px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #f0f0f0;
        text-align: center;
    }
    
    .stat-icon {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 15px;
        font-size: 20px;
    }
    
    .stat-icon.views { background: rgba(0, 123, 255, 0.1); color: #007bff; }
    .stat-icon.likes { background: rgba(255, 0, 80, 0.1); color: #FF0050; }
    .stat-icon.comments { background: rgba(40, 167, 69, 0.1); color: #28a745; }
    .stat-icon.shares { background: rgba(255, 193, 7, 0.1); color: #ffc107; }
    .stat-icon.reposts { background: rgba(23, 162, 184, 0.1); color: #17a2b8; }
    .stat-icon.videos { background: rgba(108, 117, 125, 0.1); color: #6c757d; }
    
    .stat-value {
        font-size: 28px;
        font-weight: 700;
        color: #333;
        margin-bottom: 5px;
    }
    
    .stat-label {
        font-size: 13px;
        color: #666;
    }
    
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
    
    .top-videos-table {
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        border: 1px solid #f0f0f0;
    }
    
    .top-videos-table table {
        width: 100%;
        border-collapse: collapse;
    }
    
    .top-videos-table th {
        background: #f8f9fa;
        padding: 15px;
        text-align: left;
        font-weight: 600;
        font-size: 14px;
        color: #666;
        border-bottom: 1px solid #eee;
    }
    
    .top-videos-table td {
        padding: 15px;
        border-bottom: 1px solid #f0f0f0;
        font-size: 14px;
    }
    
    .top-videos-table tr:last-child td {
        border-bottom: none;
    }
    
    .video-cell {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .video-thumb {
        width: 80px;
        height: 45px;
        border-radius: 6px;
        overflow: hidden;
        background: #f0f0f0;
        flex-shrink: 0;
    }
    
    .video-thumb img,
    .video-thumb video {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
    
    .video-title-cell {
        font-weight: 500;
        color: #333;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    
    .metric-value {
        font-weight: 600;
        color: #333;
    }
    
    .metric-value i {
        margin-right: 4px;
        font-size: 12px;
    }
    
    .metric-value.views i { color: #007bff; }
    .metric-value.likes i { color: #FF0050; }
    .metric-value.comments i { color: #28a745; }
    
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
    }
    
    @media (max-width: 768px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
        
        .top-videos-table {
            overflow-x: auto;
        }
        
        .top-videos-table table {
            min-width: 600px;
        }
    }
</style>
@endsection

@section('content')
<div class="creator-page">
    <div class="page-header">
        <h1><i class="fas fa-chart-bar me-2"></i> Analytics</h1>
    </div>
    
    <!-- Creator Navigation -->
    <nav class="creator-nav">
        <a href="{{ route('creator.dashboard') }}">
            <i class="fas fa-home me-1"></i> Overview
        </a>
        <a href="{{ route('creator.videos') }}">
            <i class="fas fa-video me-1"></i> My Videos
        </a>
        <a href="{{ route('creator.streams') }}">
            <i class="fas fa-broadcast-tower me-1"></i> Livestreams
        </a>
        <a href="{{ route('creator.analytics') }}" class="active">
            <i class="fas fa-chart-bar me-1"></i> Analytics
        </a>
        <a href="{{ route('my.settings', ['type' => 'profile']) }}">
            <i class="fas fa-cog me-1"></i> Settings
        </a>
    </nav>
    
    <!-- Overall Stats -->
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon views">
                <i class="fas fa-eye"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_views']) }}</div>
            <div class="stat-label">Total Views</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon likes">
                <i class="fas fa-heart"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_likes']) }}</div>
            <div class="stat-label">Total Likes</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon comments">
                <i class="fas fa-comment"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_comments']) }}</div>
            <div class="stat-label">Total Comments</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon shares">
                <i class="fas fa-share"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_shares']) }}</div>
            <div class="stat-label">Total Shares</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon reposts">
                <i class="fas fa-retweet"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_reposts']) }}</div>
            <div class="stat-label">Total Reposts</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon videos">
                <i class="fas fa-video"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['total_videos']) }}</div>
            <div class="stat-label">Total Videos</div>
        </div>
    </div>
    
    <!-- Averages -->
    <div class="stats-grid" style="grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));">
        <div class="stat-card">
            <div class="stat-icon views">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['avg_views']) }}</div>
            <div class="stat-label">Average Views per Video</div>
        </div>
        
        <div class="stat-card">
            <div class="stat-icon likes">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-value">{{ number_format($stats['avg_likes']) }}</div>
            <div class="stat-label">Average Likes per Video</div>
        </div>
    </div>
    
    <!-- Top Performing Videos -->
    <div class="section-header">
        <h2><i class="fas fa-trophy me-2"></i> Top Performing Videos</h2>
    </div>
    
    @if($topVideos->count() > 0)
        <div class="top-videos-table">
            <table>
                <thead>
                    <tr>
                        <th>Video</th>
                        <th>Views</th>
                        <th>Likes</th>
                        <th>Comments</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($topVideos as $video)
                        <tr>
                            <td>
                                <div class="video-cell">
                                    <div class="video-thumb">
                                        @if($video->thumbnail_url)
                                            <img src="{{ $video->thumbnail_url }}" alt="">
                                        @else
                                            <video src="{{ $video->video_url }}" muted></video>
                                        @endif
                                    </div>
                                    <span class="video-title-cell">{{ $video->title }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="metric-value views">
                                    <i class="fas fa-eye"></i>
                                    {{ number_format($video->views_count ?? 0) }}
                                </span>
                            </td>
                            <td>
                                <span class="metric-value likes">
                                    <i class="fas fa-heart"></i>
                                    {{ number_format($video->likes_count ?? 0) }}
                                </span>
                            </td>
                            <td>
                                <span class="metric-value comments">
                                    <i class="fas fa-comment"></i>
                                    {{ number_format($video->comments_count ?? 0) }}
                                </span>
                            </td>
                            <td>
                                <small class="text-muted">{{ $video->created_at->format('M d, Y') }}</small>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @else
        <div class="empty-state">
            <i class="fas fa-chart-bar"></i>
            <h3>No analytics data yet</h3>
            <p>Upload videos to start tracking your performance.</p>
        </div>
    @endif
</div>
@endsection
