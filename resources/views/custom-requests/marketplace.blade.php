@extends('layouts.user-no-nav')

@section('page_title', __('Custom Requests Marketplace'))

@section('styles')
    {!!
        Minify::stylesheet([
            '/css/pages/custom-requests-marketplace.css'
         ])->withFullUrl()
    !!}
    <link rel="stylesheet" href="{{ asset('css/pages/custom-requests-marketplace.css') }}?v=20260712b">
@stop

@section('scripts')
    <script>
        window.crMarketplaceI18n = {
            processing: @json(__('Processing...')),
            success: @json(__('Contribution added successfully!')),
            successTitle: @json(__('Success')),
            failed: @json(__('Failed to add contribution')),
            errorTitle: @json(__('Error')),
            genericError: @json(__('An error occurred. Please try again.')),
            noResults: @json(__('No requests found')),
            tryAdjusting: @json(__('Try adjusting your search or filter criteria'))
        };
    </script>
    {!!
        Minify::javascript([
            '/js/pages/custom-requests-marketplace.js'
         ])->withFullUrl()
    !!}
@stop

@section('content')
@php
    $crDark = Cookie::get('app_theme') == null
        ? getSetting('site.default_user_theme') == 'dark'
        : Cookie::get('app_theme') == 'dark';
@endphp
<div class="custom-requests-marketplace custom-requests-marketplace--{{ $crDark ? 'dark' : 'light' }}{{ $requests->count() === 0 ? ' custom-requests-marketplace--empty-layout' : '' }}">
    <div class="custom-requests-marketplace__scroll">
    <!-- Page Header -->
    <div class="marketplace-hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title d-none d-md-flex">
                        @include('elements.icon',['icon'=>'gift-outline','variant'=>'medium','classes'=>'cr-icon cr-icon--hero'])
                        {{ __('Custom Requests Marketplace') }}
                    </h1>
                    <p class="hero-subtitle">{{ __('Discover unique creator challenges and make them happen') }}</p>
                </div>
                @auth
                    <div class="hero-actions">
                        <button class="btn btn-hero-primary" onclick="CustomRequest.showCreateModal()">
                            @include('elements.icon',['icon'=>'add-circle-outline','variant'=>'small','classes'=>'cr-icon'])
                            <span>{{ __('Create Request') }}</span>
                        </button>
                        <a href="{{ route('custom-requests.my-requests') }}" class="btn btn-hero-secondary">
                            @include('elements.icon',['icon'=>'list-outline','variant'=>'small','classes'=>'cr-icon'])
                            <span>{{ __('My Requests') }}</span>
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <div class="container marketplace-container{{ $requests->count() === 0 ? ' marketplace-container--empty' : '' }}">
        <!-- Stats Overview Cards -->
        <div class="stats-grid">
            <div class="stat-card stat-primary">
                <div class="stat-icon">
                    @include('elements.icon',['icon'=>'locate-outline','variant'=>'small','classes'=>'cr-icon cr-icon--stat'])
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $requests->total() }}</h3>
                    <p class="stat-label">{{ __('Active Requests') }}</p>
                </div>
            </div>
            <div class="stat-card stat-success">
                <div class="stat-icon">
                    @include('elements.icon',['icon'=>'heart-outline','variant'=>'small','classes'=>'cr-icon cr-icon--stat'])
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">${{ number_format($requests->sum('current_amount'), 0) }}</h3>
                    <p class="stat-label">{{ __('Total Raised') }}</p>
                </div>
            </div>
            <div class="stat-card stat-info">
                <div class="stat-icon">
                    @include('elements.icon',['icon'=>'people-outline','variant'=>'small','classes'=>'cr-icon cr-icon--stat'])
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $requests->sum(function($r) { return $r->contributions->count(); }) }}</h3>
                    <p class="stat-label">{{ __('Contributions') }}</p>
                </div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-icon">
                    @include('elements.icon',['icon'=>'trophy-outline','variant'=>'small','classes'=>'cr-icon cr-icon--stat'])
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $requests->where('status', 'completed')->count() }}</h3>
                    <p class="stat-label">{{ __('Completed') }}</p>
                </div>
            </div>
        </div>

        <!-- Search and Filter Section -->
        <div class="filter-section">
            <div class="search-box">
                <span class="search-icon" aria-hidden="true">
                    @include('elements.icon',['icon'=>'search-outline','variant'=>'small','centered'=>false,'classes'=>'cr-icon cr-icon--search'])
                </span>
                <input type="text" 
                       class="search-input" 
                       id="searchInput" 
                       placeholder="{{ __('Search requests...') }}"
                       autocomplete="off">
                <button class="search-clear" id="clearSearch" type="button" style="display: none;" aria-label="{{ __('Clear search') }}">
                    @include('elements.icon',['icon'=>'close-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                </button>
            </div>
            <div class="filter-controls">
                <div class="filter-field">
                    <select class="filter-select" id="statusFilter" aria-label="{{ __('Filter by status') }}">
                        <option value="">{{ __('All Status') }}</option>
                        <option value="accepted">{{ __('Active') }}</option>
                        <option value="completed">{{ __('Completed') }}</option>
                        <option value="pending">{{ __('Pending') }}</option>
                        <option value="cancelled">{{ __('Cancelled') }}</option>
                    </select>
                </div>
                <div class="filter-field">
                    <select class="filter-select" id="sortBy" aria-label="{{ __('Sort requests') }}">
                        <option value="newest">{{ __('Newest First') }}</option>
                        <option value="oldest">{{ __('Oldest First') }}</option>
                        <option value="most-funded">{{ __('Most Funded') }}</option>
                        <option value="closest-goal">{{ __('Closest to Goal') }}</option>
                        <option value="highest-goal">{{ __('Highest Goal') }}</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Requests Grid -->
        <div class="requests-grid{{ $requests->count() === 0 ? ' requests-grid--empty' : '' }}" id="requestsContainer">
            @forelse($requests as $request)
                <div class="request-card" 
                     data-status="{{ $request->status }}"
                     data-title="{{ strtolower($request->title) }}"
                     data-description="{{ strtolower($request->description) }}"
                     data-progress="{{ $request->progress_percentage ?? 0 }}"
                     data-amount="{{ $request->current_amount ?? 0 }}">
                    
                    <!-- Card Header -->
                    <div class="card-header-section">
                        <div class="status-badge status-{{ $request->status }}">
                            @if($request->status == 'accepted')
                                @include('elements.icon',['icon'=>'play-circle-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                                <span>{{ __('Active') }}</span>
                            @elseif($request->status == 'completed')
                                @include('elements.icon',['icon'=>'checkmark-circle-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                                <span>{{ __('Completed') }}</span>
                            @elseif($request->status == 'cancelled')
                                @include('elements.icon',['icon'=>'close-circle-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                                <span>{{ __('Cancelled') }}</span>
                            @else
                                @include('elements.icon',['icon'=>'time-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                                <span>{{ __('Pending') }}</span>
                            @endif
                        </div>
                        <div class="card-menu">
                            <button class="menu-btn" type="button" data-toggle="dropdown">
                                @include('elements.icon',['icon'=>'ellipsis-vertical','variant'=>'xsmall','classes'=>'cr-icon'])
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('custom-requests.show', $request->id) }}">
                                    @include('elements.icon',['icon'=>'eye-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                                    <span>{{ __('View Details') }}</span>
                                </a>
                                <a class="dropdown-item" href="{{ route('profile', ['username' => $request->creator->username ?? '']) }}">
                                    @include('elements.icon',['icon'=>'person-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                                    <span>{{ __('View Creator') }}</span>
                                </a>
                            </div>
                        </div>
                    </div>

                    <!-- Creator Info -->
                    <div class="creator-section">
                        <div class="creator-avatar">
                            @if(isset($request->creator) && $request->creator->avatar)
                                <img src="{{ $request->creator->avatar }}" alt="{{ $request->creator->name }}">
                            @else
                                <div class="avatar-placeholder">
                                    @include('elements.icon',['icon'=>'person-outline','variant'=>'small','classes'=>'cr-icon'])
                                </div>
                            @endif
                        </div>
                        <div class="creator-info">
                            <h6 class="creator-name">
                                @if(isset($request->creator))
                                    <a href="{{ route('profile', ['username' => $request->creator->username]) }}">
                                        {{ $request->creator->name }}
                                    </a>
                                @else
                                    {{ __('Unknown Creator') }}
                                @endif
                            </h6>
                            <span class="creator-time">
                                @include('elements.icon',['icon'=>'time-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                                {{ $request->created_at->diffForHumans() }}
                            </span>
                        </div>
                    </div>

                    <!-- Request Content -->
                    <div class="card-content">
                        <h5 class="request-title">
                            <a href="{{ route('custom-requests.show', $request->id) }}">
                                {{ Str::limit($request->title, 60) }}
                            </a>
                        </h5>
                        <p class="request-description">
                            {{ Str::limit($request->description, 120) }}
                        </p>
                    </div>

                    <!-- Progress Section -->
                    <div class="progress-section">
                        <div class="progress-header">
                            <span class="progress-label">{{ __('Funding Progress') }}</span>
                            <span class="progress-percentage">{{ number_format($request->progress_percentage ?? 0, 1) }}%</span>
                        </div>
                        <div class="progress-bar-wrapper">
                            <div class="progress-bar-fill" style="width: {{ min(100, $request->progress_percentage ?? 0) }}%"></div>
                        </div>
                        <div class="progress-amounts">
                            <span class="amount-raised">
                                @include('elements.icon',['icon'=>'cash-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                                <strong>${{ number_format($request->current_amount ?? 0, 0) }}</strong>
                                <small>{{ __('raised') }}</small>
                            </span>
                            <span class="amount-goal">
                                @include('elements.icon',['icon'=>'locate-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                                <strong>${{ number_format($request->goal_amount ?? 0, 0) }}</strong>
                                <small>{{ __('goal') }}</small>
                            </span>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer-section">
                        <a href="{{ route('custom-requests.show', $request->id) }}" class="btn btn-view">
                            @include('elements.icon',['icon'=>'eye-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                            <span>{{ __('View Details') }}</span>
                        </a>
                        @auth
                            @if($request->status == 'accepted' && $request->creator_id != Auth::id())
                                <button class="btn btn-contribute" 
                                        data-request-id="{{ $request->id }}"
                                        data-title="{{ $request->title }}">
                                    @include('elements.icon',['icon'=>'heart-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                                    <span>{{ __('Contribute') }}</span>
                                </button>
                            @elseif($request->creator_id == Auth::id())
                                <span class="btn btn-owner">
                                    @include('elements.icon',['icon'=>'create-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                                    <span>{{ __('Your Request') }}</span>
                                </span>
                            @endif
                        @endauth
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        @include('elements.icon',['icon'=>'gift-outline','variant'=>'medium','classes'=>'cr-icon cr-icon--empty'])
                    </div>
                    <h3>{{ __('No custom requests found') }}</h3>
                    <p>{{ __('Be the first to create an amazing custom request!') }}</p>
                    @auth
                        <button class="btn btn-primary btn-lg" onclick="CustomRequest.showCreateModal()">
                            @include('elements.icon',['icon'=>'add-circle-outline','variant'=>'small','classes'=>'cr-icon'])
                            {{ __('Create First Request') }}
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                            @include('elements.icon',['icon'=>'log-in-outline','variant'=>'small','classes'=>'cr-icon'])
                            {{ __('Login to Create') }}
                        </a>
                    @endauth
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($requests->hasPages())
            <div class="pagination-section">
                {{ $requests->links() }}
            </div>
        @endif
    </div>
    </div>
</div>

@auth
<!-- Enhanced Contribution Modal -->
<div class="modal fade" id="contributeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content contribute-modal-content">
            <div class="modal-header contribute-header">
                <div class="header-icon-wrapper">
                    @include('elements.icon',['icon'=>'heart-outline','variant'=>'small','classes'=>'cr-icon'])
                </div>
                <h5 class="modal-title">{{ __('Contribute to Request') }}</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="contributeForm">
                <div class="modal-body contribute-body">
                    <div class="request-preview-card">
                        <div class="preview-icon">
                            @include('elements.icon',['icon'=>'gift-outline','variant'=>'small','classes'=>'cr-icon'])
                        </div>
                        <h6 id="modal-request-title" class="preview-title"></h6>
                        <p class="preview-subtitle">{{ __('Help make this request a reality!') }}</p>
                    </div>
                    
                    <input type="hidden" id="request_id" name="request_id">
                    
                    <div class="form-group">
                        <label for="amount" class="form-label">
                            @include('elements.icon',['icon'=>'cash-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                            {{ __('Contribution Amount') }}
                        </label>
                        <div class="amount-input-wrapper">
                            <span class="currency-symbol">$</span>
                            <input type="number" 
                                   class="form-control amount-input" 
                                   id="amount" 
                                   name="amount" 
                                   step="0.01" 
                                   min="0.01" 
                                   required 
                                   placeholder="0.00">
                        </div>
                        <small class="form-text">{{ __('Enter the amount you would like to contribute') }}</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="message" class="form-label">
                            @include('elements.icon',['icon'=>'chatbubble-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                            {{ __('Message') }} <span class="text-muted">({{ __('Optional') }})</span>
                        </label>
                        <textarea class="form-control" 
                                  id="message" 
                                  name="message" 
                                  rows="3" 
                                  placeholder="{{ __('Leave an encouraging message for the creator...') }}"></textarea>
                    </div>
                </div>
                <div class="modal-footer contribute-footer">
                    <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                        @include('elements.icon',['icon'=>'close-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary btn-contribute-submit">
                        @include('elements.icon',['icon'=>'heart-outline','variant'=>'xsmall','classes'=>'cr-icon'])
                        {{ __('Make Contribution') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth
@endsection
