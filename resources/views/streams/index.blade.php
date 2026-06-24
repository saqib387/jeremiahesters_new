@extends('layouts.generic')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header d-flex justify-content-between align-items-center bg-white">
                    <h2 class="mb-0">Live Streams</h2>
                    @auth
                        <a href="{{ route('streams.create') }}" class="btn btn-primary">
                            <i class="fas fa-video me-2"></i>
                            Start Streaming
                        </a>
                    @endauth
                </div>

                <div class="card-body">
                    @if($streams->count() > 0)
                        <div class="row">
                            @foreach($streams as $stream)
                                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                                    <div class="card h-100 stream-card shadow-sm">
                                        <div class="card-body p-0">
                                            <div class="stream-preview position-relative">
                                                @if($stream->thumbnail)
                                                    <img src="{{ asset('storage/' . $stream->thumbnail) }}" class="w-100" style="height: 200px; object-fit: cover;" alt="{{ $stream->title }}">
                                                @else
                                                    <div class="bg-dark d-flex justify-content-center align-items-center" style="height: 200px;">
                                                        <i class="fas fa-video fa-3x text-light opacity-50"></i>
                                                    </div>
                                                @endif
                                                @if($stream->is_live)
                                                    <span class="badge bg-danger position-absolute top-0 end-0 m-2 live-badge">LIVE</span>
                                                @endif
                                                @if($stream->requires_subscription)
                                                    <span class="badge bg-primary position-absolute top-0 start-0 m-2">
                                                        <i class="fas fa-lock me-1"></i> Subscribers
                                                    </span>
                                                @endif
                                                @if($stream->price > 0)
                                                    <span class="badge bg-success position-absolute bottom-0 start-0 m-2">
                                                        ${{ number_format($stream->price, 2) }}
                                                    </span>
                                                @endif
                                                <a href="{{ route('streams.watch', $stream) }}" class="stream-overlay position-absolute top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center">
                                                    <div class="play-button">
                                                        <i class="fas fa-play"></i>
                                                    </div>
                                                </a>
                                            </div>
                                            <div class="p-3">
                                                <div class="d-flex align-items-center mb-2">
                                                    <img src="{{ $stream->user->avatar ?? asset('img/default-avatar.png') }}" alt="{{ $stream->user->name }}" class="rounded-circle me-2" width="32" height="32">
                                                    <div>
                                                        <h6 class="mb-0 text-truncate">{{ $stream->user->name }}</h6>
                                                        <small class="text-muted">{{ '@' . $stream->user->username }}</small>
                                                    </div>
                                                </div>
                                                <h5 class="card-title mb-1 text-truncate">{{ $stream->title }}</h5>
                                                <p class="card-text small text-muted mb-0">
                                                    <i class="fas fa-eye me-1"></i> {{ $stream->viewer_count ?? 0 }} viewers
                                                    <span class="ms-2">
                                                        <i class="fas fa-clock me-1"></i> 
                                                        {{ $stream->started_at ? $stream->started_at->diffForHumans() : 'Not started' }}
                                                    </span>
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="d-flex justify-content-center mt-4">
                            {{ $streams->links() }}
                        </div>
                    @else
                        <div class="text-center py-5">
                            <img src="{{ asset('img/no-streams.svg') }}" alt="No streams" class="mb-4" style="max-width: 200px; opacity: 0.5;">
                            <h3>No Live Streams Available</h3>
                            <p class="text-muted">Check back later or start your own stream!</p>
                            @auth
                                <a href="{{ route('streams.create') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-video me-2"></i>
                                    Start Streaming
                                </a>
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary mt-2">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Login to Stream
                                </a>
                            @endauth
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h4 class="mb-0">How to Start Streaming</h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="fas fa-laptop fa-3x text-primary"></i>
                                </div>
                                <h5>1. Set Up Your Stream</h5>
                                <p class="text-muted">Create a new stream and get your unique streaming key</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="fas fa-cogs fa-3x text-primary"></i>
                                </div>
                                <h5>2. Configure Your Software</h5>
                                <p class="text-muted">Use OBS, Streamlabs or any RTMP compatible software</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <div class="mb-3">
                                    <i class="fas fa-broadcast-tower fa-3x text-primary"></i>
                                </div>
                                <h5>3. Go Live</h5>
                                <p class="text-muted">Start broadcasting and connect with your audience</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .stream-card {
        transition: transform 0.2s ease;
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .stream-card:hover {
        transform: translateY(-5px);
    }
    
    .stream-overlay {
        background: rgba(0, 0, 0, 0.4);
        opacity: 0;
        transition: opacity 0.3s ease;
    }
    
    .stream-card:hover .stream-overlay {
        opacity: 1;
    }
    
    .play-button {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.9);
        border-radius: 50%;
        display: flex;
        justify-content: center;
        align-items: center;
    }
    
    .play-button i {
        color: #4A6CF7;
        font-size: 24px;
        margin-left: 4px;
    }
    
    .live-badge {
        animation: pulse 1.5s infinite;
    }
    
    @keyframes pulse {
        0% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
        100% {
            opacity: 1;
        }
    }
</style>
@endpush
@endsection 