@extends('layouts.user-no-nav')

@section('page_title', __('Custom Requests Marketplace'))

@section('content')
<div class="custom-requests-marketplace">
    <!-- Hero Header Section -->
    <div class="marketplace-hero">
        <div class="container">
            <div class="hero-content">
                <div class="hero-text">
                    <h1 class="hero-title">
                        <i class="fas fa-gift"></i>
                        {{ __('Custom Requests Marketplace') }}
                    </h1>
                    <p class="hero-subtitle">{{ __('Discover unique creator challenges and make them happen') }}</p>
                </div>
                @auth
                    <div class="hero-actions">
                        <button class="btn btn-hero-primary" onclick="CustomRequest.showCreateModal()">
                            <i class="fas fa-plus-circle"></i>
                            <span>{{ __('Create Request') }}</span>
                        </button>
                        <a href="{{ route('custom-requests.my-requests') }}" class="btn btn-hero-secondary">
                            <i class="fas fa-list"></i>
                            <span>{{ __('My Requests') }}</span>
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <div class="container marketplace-container">
        <!-- Stats Overview Cards -->
        <div class="stats-grid">
            <div class="stat-card stat-primary">
                <div class="stat-icon">
                    <i class="fas fa-bullseye"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $requests->total() }}</h3>
                    <p class="stat-label">{{ __('Active Requests') }}</p>
                </div>
            </div>
            <div class="stat-card stat-success">
                <div class="stat-icon">
                    <i class="fas fa-hand-holding-heart"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">${{ number_format($requests->sum('current_amount'), 0) }}</h3>
                    <p class="stat-label">{{ __('Total Raised') }}</p>
                </div>
            </div>
            <div class="stat-card stat-info">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $requests->sum(function($r) { return $r->contributions->count(); }) }}</h3>
                    <p class="stat-label">{{ __('Contributions') }}</p>
                </div>
            </div>
            <div class="stat-card stat-warning">
                <div class="stat-icon">
                    <i class="fas fa-trophy"></i>
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
                <i class="fas fa-search search-icon"></i>
                <input type="text" 
                       class="search-input" 
                       id="searchInput" 
                       placeholder="{{ __('Search requests by title or description...') }}">
                <button class="search-clear" id="clearSearch" style="display: none;">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="filter-controls">
                <select class="filter-select" id="statusFilter">
                    <option value="">{{ __('All Status') }}</option>
                    <option value="accepted">{{ __('Active') }}</option>
                    <option value="completed">{{ __('Completed') }}</option>
                    <option value="pending">{{ __('Pending') }}</option>
                    <option value="cancelled">{{ __('Cancelled') }}</option>
                </select>
                <select class="filter-select" id="sortBy">
                    <option value="newest">{{ __('Newest First') }}</option>
                    <option value="oldest">{{ __('Oldest First') }}</option>
                    <option value="most-funded">{{ __('Most Funded') }}</option>
                    <option value="closest-goal">{{ __('Closest to Goal') }}</option>
                    <option value="highest-goal">{{ __('Highest Goal') }}</option>
                </select>
            </div>
        </div>

        <!-- Requests Grid -->
        <div class="requests-grid" id="requestsContainer">
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
                                <i class="fas fa-play-circle"></i>
                                <span>{{ __('Active') }}</span>
                            @elseif($request->status == 'completed')
                                <i class="fas fa-check-circle"></i>
                                <span>{{ __('Completed') }}</span>
                            @elseif($request->status == 'cancelled')
                                <i class="fas fa-times-circle"></i>
                                <span>{{ __('Cancelled') }}</span>
                            @else
                                <i class="fas fa-clock"></i>
                                <span>{{ __('Pending') }}</span>
                            @endif
                        </div>
                        <div class="card-menu">
                            <button class="menu-btn" type="button" data-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-right">
                                <a class="dropdown-item" href="{{ route('custom-requests.show', $request->id) }}">
                                    <i class="fas fa-eye"></i>
                                    <span>{{ __('View Details') }}</span>
                                </a>
                                <a class="dropdown-item" href="{{ route('profile', ['username' => $request->creator->username ?? '']) }}">
                                    <i class="fas fa-user"></i>
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
                                    <i class="fas fa-user"></i>
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
                                <i class="far fa-clock"></i>
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
                                <i class="fas fa-dollar-sign"></i>
                                <strong>${{ number_format($request->current_amount ?? 0, 0) }}</strong>
                                <small>{{ __('raised') }}</small>
                            </span>
                            <span class="amount-goal">
                                <i class="fas fa-bullseye"></i>
                                <strong>${{ number_format($request->goal_amount ?? 0, 0) }}</strong>
                                <small>{{ __('goal') }}</small>
                            </span>
                        </div>
                    </div>

                    <!-- Card Footer -->
                    <div class="card-footer-section">
                        <a href="{{ route('custom-requests.show', $request->id) }}" class="btn btn-view">
                            <i class="fas fa-eye"></i>
                            <span>{{ __('View Details') }}</span>
                        </a>
                        @auth
                            @if($request->status == 'accepted' && $request->creator_id != Auth::id())
                                <button class="btn btn-contribute" 
                                        data-request-id="{{ $request->id }}"
                                        data-title="{{ $request->title }}">
                                    <i class="fas fa-heart"></i>
                                    <span>{{ __('Contribute') }}</span>
                                </button>
                            @elseif($request->creator_id == Auth::id())
                                <span class="btn btn-owner">
                                    <i class="fas fa-user-edit"></i>
                                    <span>{{ __('Your Request') }}</span>
                                </span>
                            @endif
                        @endauth
                    </div>
                </div>
            @empty
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <h3>{{ __('No custom requests found') }}</h3>
                    <p>{{ __('Be the first to create an amazing custom request!') }}</p>
                    @auth
                        <button class="btn btn-primary btn-lg" onclick="CustomRequest.showCreateModal()">
                            <i class="fas fa-plus-circle"></i>
                            {{ __('Create First Request') }}
                        </button>
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg">
                            <i class="fas fa-sign-in-alt"></i>
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

@auth
<!-- Enhanced Contribution Modal -->
<div class="modal fade" id="contributeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content contribute-modal-content">
            <div class="modal-header contribute-header">
                <div class="header-icon-wrapper">
                    <i class="fas fa-heart"></i>
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
                            <i class="fas fa-gift"></i>
                        </div>
                        <h6 id="modal-request-title" class="preview-title"></h6>
                        <p class="preview-subtitle">{{ __('Help make this request a reality!') }}</p>
                    </div>
                    
                    <input type="hidden" id="request_id" name="request_id">
                    
                    <div class="form-group">
                        <label for="amount" class="form-label">
                            <i class="fas fa-dollar-sign"></i>
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
                            <i class="fas fa-comment"></i>
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
                        <i class="fas fa-times"></i>
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary btn-contribute-submit">
                        <i class="fas fa-heart"></i>
                        {{ __('Make Contribution') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endauth

<style>
/* Marketplace Container */
.custom-requests-marketplace {
    min-height: 100vh;
    background: linear-gradient(180deg, #f7fafc 0%, #ffffff 100%);
}

/* Hero Section */
.marketplace-hero {
    background: linear-gradient(135deg, #830866 0%, #a10a7f 100%);
    padding: 4rem 0 3rem;
    margin-bottom: 3rem;
    position: relative;
    overflow: hidden;
}

.marketplace-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url('data:image/svg+xml,<svg width="100" height="100" xmlns="http://www.w3.org/2000/svg"><defs><pattern id="grid" width="100" height="100" patternUnits="userSpaceOnUse"><path d="M 100 0 L 0 0 0 100" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="1"/></pattern></defs><rect width="100%" height="100%" fill="url(%23grid)"/></svg>');
    opacity: 0.3;
}

.hero-content {
    position: relative;
    z-index: 1;
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 2rem;
}

.hero-title {
    font-size: 2.5rem;
    font-weight: 800;
    color: white;
    margin: 0 0 0.5rem 0;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.hero-title i {
    font-size: 2rem;
    opacity: 0.9;
}

.hero-subtitle {
    font-size: 1.1rem;
    color: rgba(255, 255, 255, 0.9);
    margin: 0;
}

.hero-actions {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.btn-hero-primary,
.btn-hero-secondary {
    padding: 0.875rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    font-size: 1rem;
    border: none;
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    text-decoration: none;
}

.btn-hero-primary {
    background: white;
    color: #830866;
}

.btn-hero-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
    color: #830866;
}

.btn-hero-secondary {
    background: rgba(255, 255, 255, 0.2);
    color: white;
    backdrop-filter: blur(10px);
}

.btn-hero-secondary:hover {
    background: rgba(255, 255, 255, 0.3);
    color: white;
    transform: translateY(-2px);
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 2.5rem;
}

.stat-card {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 1px solid #e2e8f0;
}

.stat-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
}

.stat-icon {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.75rem;
    flex-shrink: 0;
}

.stat-primary .stat-icon {
    background: linear-gradient(135deg, #830866 0%, #a10a7f 100%);
    color: white;
}

.stat-success .stat-icon {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    color: white;
}

.stat-info .stat-icon {
    background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
    color: white;
}

.stat-warning .stat-icon {
    background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
    color: white;
}

.stat-number {
    font-size: 1.75rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0;
    line-height: 1.2;
}

.stat-label {
    font-size: 0.875rem;
    color: #718096;
    margin: 0.25rem 0 0 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Filter Section */
.filter-section {
    background: white;
    border-radius: 16px;
    padding: 1.5rem;
    margin-bottom: 2rem;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
}

.search-box {
    position: relative;
    margin-bottom: 1rem;
}

.search-icon {
    position: absolute;
    left: 1.25rem;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
    font-size: 1.1rem;
    z-index: 1;
}

.search-input {
    width: 100%;
    padding: 1rem 3rem 1rem 3.5rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #f7fafc;
}

.search-input:focus {
    outline: none;
    border-color: #830866;
    background: white;
    box-shadow: 0 0 0 4px rgba(131, 8, 102, 0.1);
}

.search-clear {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    background: #e2e8f0;
    border: none;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #718096;
    cursor: pointer;
    transition: all 0.2s;
}

.search-clear:hover {
    background: #cbd5e0;
    color: #2d3748;
}

.filter-controls {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.filter-select {
    flex: 1;
    min-width: 150px;
    padding: 0.75rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    font-size: 0.95rem;
    background: white;
    cursor: pointer;
    transition: all 0.3s ease;
}

.filter-select:focus {
    outline: none;
    border-color: #830866;
    box-shadow: 0 0 0 4px rgba(131, 8, 102, 0.1);
}

/* Requests Grid */
.requests-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
    gap: 2rem;
    margin-bottom: 3rem;
}

.request-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    border: 1px solid #e2e8f0;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    animation: fadeInUp 0.6s ease-out;
}

.request-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 32px rgba(131, 8, 102, 0.15);
    border-color: #830866;
}

/* Card Header */
.card-header-section {
    padding: 1.25rem 1.25rem 0;
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.status-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 20px;
    font-size: 0.8rem;
    font-weight: 600;
    color: white;
}

.status-accepted {
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
}

.status-completed {
    background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
}

.status-pending {
    background: linear-gradient(135deg, #ed8936 0%, #dd6b20 100%);
}

.status-cancelled {
    background: linear-gradient(135deg, #a0aec0 0%, #718096 100%);
}

.menu-btn {
    background: #f7fafc;
    border: none;
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #718096;
    cursor: pointer;
    transition: all 0.2s;
}

.menu-btn:hover {
    background: #e2e8f0;
    color: #2d3748;
}

.dropdown-menu {
    border: none;
    box-shadow: 0 8px 24px rgba(0, 0, 0, 0.12);
    border-radius: 12px;
    padding: 0.5rem;
    margin-top: 0.5rem;
}

.dropdown-item {
    padding: 0.75rem 1rem;
    border-radius: 8px;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    transition: all 0.2s;
}

.dropdown-item:hover {
    background: #f7fafc;
}

.dropdown-item i {
    width: 20px;
    color: #830866;
}

/* Creator Section */
.creator-section {
    padding: 1rem 1.25rem;
    display: flex;
    align-items: center;
    gap: 1rem;
    border-bottom: 1px solid #e2e8f0;
}

.creator-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    overflow: hidden;
    flex-shrink: 0;
    border: 2px solid #e2e8f0;
}

.creator-avatar img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.avatar-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #830866 0%, #a10a7f 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.25rem;
}

.creator-name {
    font-size: 0.95rem;
    font-weight: 600;
    margin: 0 0 0.25rem 0;
}

.creator-name a {
    color: #2d3748;
    text-decoration: none;
    transition: color 0.2s;
}

.creator-name a:hover {
    color: #830866;
}

.creator-time {
    font-size: 0.8rem;
    color: #718096;
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

/* Card Content */
.card-content {
    padding: 1.25rem;
    flex-grow: 1;
}

.request-title {
    font-size: 1.1rem;
    font-weight: 700;
    margin: 0 0 0.75rem 0;
    line-height: 1.4;
}

.request-title a {
    color: #2d3748;
    text-decoration: none;
    transition: color 0.2s;
}

.request-title a:hover {
    color: #830866;
}

.request-description {
    color: #718096;
    font-size: 0.9rem;
    line-height: 1.6;
    margin: 0;
}

/* Progress Section */
.progress-section {
    padding: 1.25rem;
    background: #f7fafc;
    border-top: 1px solid #e2e8f0;
}

.progress-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 0.75rem;
}

.progress-label {
    font-size: 0.85rem;
    color: #718096;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.progress-percentage {
    font-size: 0.9rem;
    font-weight: 700;
    color: #830866;
}

.progress-bar-wrapper {
    height: 10px;
    background: #e2e8f0;
    border-radius: 10px;
    overflow: hidden;
    margin-bottom: 0.75rem;
}

.progress-bar-fill {
    height: 100%;
    background: linear-gradient(135deg, #830866 0%, #a10a7f 100%);
    border-radius: 10px;
    transition: width 0.6s ease;
}

.progress-amounts {
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.amount-raised,
.amount-goal {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.9rem;
}

.amount-raised {
    color: #48bb78;
}

.amount-raised i,
.amount-goal i {
    font-size: 0.8rem;
}

.amount-raised strong,
.amount-goal strong {
    font-size: 1.1rem;
    font-weight: 700;
}

.amount-goal {
    color: #718096;
}

.amount-goal small,
.amount-raised small {
    font-size: 0.75rem;
    opacity: 0.8;
}

/* Card Footer */
.card-footer-section {
    padding: 1.25rem;
    display: flex;
    gap: 0.75rem;
    border-top: 1px solid #e2e8f0;
}

.btn-view,
.btn-contribute,
.btn-owner {
    flex: 1;
    padding: 0.75rem 1rem;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.9rem;
    border: none;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
    text-decoration: none;
    cursor: pointer;
}

.btn-view {
    background: #f7fafc;
    color: #830866;
    border: 2px solid #e2e8f0;
}

.btn-view:hover {
    background: #e2e8f0;
    border-color: #cbd5e0;
    transform: translateY(-2px);
    color: #830866;
    text-decoration: none;
}

.btn-contribute {
    background: linear-gradient(135deg, #830866 0%, #a10a7f 100%);
    color: white;
}

.btn-contribute:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(131, 8, 102, 0.3);
    color: white;
}

.btn-owner {
    background: #edf2f7;
    color: #718096;
    cursor: default;
}

/* Empty State */
.empty-state {
    grid-column: 1 / -1;
    text-align: center;
    padding: 4rem 2rem;
}

.empty-icon {
    width: 120px;
    height: 120px;
    margin: 0 auto 2rem;
    background: linear-gradient(135deg, #830866 0%, #a10a7f 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    color: white;
    opacity: 0.8;
}

.empty-state h3 {
    font-size: 1.5rem;
    color: #2d3748;
    margin: 0 0 0.5rem 0;
}

.empty-state p {
    color: #718096;
    font-size: 1rem;
    margin: 0 0 2rem 0;
}

/* Contribution Modal */
.contribute-modal-content {
    border-radius: 20px;
    border: none;
    overflow: hidden;
}

.contribute-header {
    background: linear-gradient(135deg, #830866 0%, #a10a7f 100%);
    color: white;
    border-bottom: none;
    padding: 2rem;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-icon-wrapper {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.contribute-header .modal-title {
    flex: 1;
    margin: 0;
    font-weight: 700;
    color: white;
}

.contribute-header .close {
    color: white;
    opacity: 0.9;
    font-size: 1.5rem;
}

.request-preview-card {
    background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    margin-bottom: 1.5rem;
}

.preview-icon {
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #830866 0%, #a10a7f 100%);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 1rem;
    font-size: 1.5rem;
    color: white;
}

.preview-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: #2d3748;
    margin: 0 0 0.5rem 0;
}

.preview-subtitle {
    color: #718096;
    font-size: 0.9rem;
    margin: 0;
}

.amount-input-wrapper {
    position: relative;
}

.currency-symbol {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    font-size: 1.25rem;
    font-weight: 600;
    color: #830866;
    z-index: 1;
}

.amount-input {
    padding-left: 2.5rem !important;
    font-size: 1.25rem;
    font-weight: 600;
}

.contribute-footer {
    border-top: 1px solid #e2e8f0;
    padding: 1.5rem;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.btn-contribute-submit {
    background: linear-gradient(135deg, #830866 0%, #a10a7f 100%);
    border: none;
    padding: 0.75rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    color: white;
}

.btn-contribute-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(131, 8, 102, 0.3);
    color: white;
}

/* Pagination */
.pagination-section {
    margin-top: 3rem;
    display: flex;
    justify-content: center;
}

.pagination-section .pagination {
    gap: 0.5rem;
}

.pagination-section .page-link {
    border-radius: 10px;
    border: 2px solid #e2e8f0;
    color: #830866;
    padding: 0.75rem 1rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.pagination-section .page-item.active .page-link {
    background: linear-gradient(135deg, #830866 0%, #a10a7f 100%);
    border-color: #830866;
    color: white;
}

.pagination-section .page-link:hover {
    border-color: #830866;
    background: #f7fafc;
}

/* Animations */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.request-card.hidden {
    display: none;
}

/* Responsive */
@media (max-width: 768px) {
    .hero-title {
        font-size: 2rem;
    }
    
    .hero-subtitle {
        font-size: 1rem;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .requests-grid {
        grid-template-columns: 1fr;
        gap: 1.5rem;
    }
    
    .filter-controls {
        flex-direction: column;
    }
    
    .filter-select {
        width: 100%;
    }
}

@media (max-width: 576px) {
    .marketplace-hero {
        padding: 2rem 0;
    }
    
    .hero-content {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .card-footer-section {
        flex-direction: column;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Contribution Modal
    const contributeBtns = document.querySelectorAll('.btn-contribute');
    const contributeModalElement = document.getElementById('contributeModal');
    let contributeModal = null;
    
    if (contributeModalElement) {
        if (typeof bootstrap !== 'undefined' && bootstrap.Modal) {
            contributeModal = new bootstrap.Modal(contributeModalElement);
        } else if (typeof $ !== 'undefined' && $.fn.modal) {
            contributeModal = { show: () => $(contributeModalElement).modal('show'), hide: () => $(contributeModalElement).modal('hide') };
        }
    }
    
    const contributeForm = document.getElementById('contributeForm');
    const modalTitle = document.getElementById('modal-request-title');

    contributeBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const requestId = this.getAttribute('data-request-id');
            const requestTitle = this.getAttribute('data-title');

            if (document.getElementById('request_id')) {
                document.getElementById('request_id').value = requestId;
            }
            if (modalTitle) {
                modalTitle.textContent = requestTitle;
            }

            if (contributeForm) {
                contributeForm.reset();
            }

            if (contributeModal) {
                contributeModal.show();
            }
        });
    });

    if (contributeForm) {
        contributeForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const requestId = document.getElementById('request_id').value;
            const amount = document.getElementById('amount').value;
            const message = document.getElementById('message').value;

            const submitBtn = contributeForm.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> {{ __('Processing...') }}';

            fetch(`/custom-requests/${requestId}/contribute`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    amount: amount,
                    message: message
                })
            })
            .then(async response => {
                const contentType = response.headers.get('content-type');
                const isJson = contentType && contentType.includes('application/json');
                
                if (isJson) {
                    return response.json();
                } else {
                    throw { message: 'Invalid response from server' };
                }
            })
            .then(data => {
                if (data.success) {
                    if (typeof launchToast !== 'undefined') {
                        launchToast('success', '{{ __('Success') }}', data.message || '{{ __('Contribution added successfully!') }}');
                    } else {
                        alert(data.message || '{{ __('Contribution added successfully!') }}');
                    }
                    if (contributeModal) {
                        contributeModal.hide();
                    }
                    setTimeout(() => location.reload(), 1500);
                } else {
                    const errorMsg = data.message || '{{ __('Failed to add contribution') }}';
                    if (typeof launchToast !== 'undefined') {
                        launchToast('danger', '{{ __('Error') }}', errorMsg);
                    } else {
                        alert('{{ __('Error') }}: ' + errorMsg);
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalText;
                }
            })
            .catch(error => {
                console.error('Error:', error);
                const errorMsg = error.message || '{{ __('An error occurred. Please try again.') }}';
                if (typeof launchToast !== 'undefined') {
                    launchToast('danger', '{{ __('Error') }}', errorMsg);
                } else {
                    alert(errorMsg);
                }
                submitBtn.disabled = false;
                submitBtn.innerHTML = originalText;
            });
        });
    }

    // Search and Filter
    const searchInput = document.getElementById('searchInput');
    const clearSearch = document.getElementById('clearSearch');
    const statusFilter = document.getElementById('statusFilter');
    const sortBy = document.getElementById('sortBy');
    const requestItems = document.querySelectorAll('.request-card');

    // Clear search button
    if (searchInput && clearSearch) {
        searchInput.addEventListener('input', function() {
            clearSearch.style.display = this.value ? 'flex' : 'none';
        });
        
        clearSearch.addEventListener('click', function() {
            searchInput.value = '';
            clearSearch.style.display = 'none';
            filterRequests();
        });
    }

    function filterRequests() {
        const searchTerm = (searchInput?.value || '').toLowerCase();
        const statusValue = statusFilter?.value || '';
        const sortValue = sortBy?.value || 'newest';

        let visibleCount = 0;

        requestItems.forEach(item => {
            const title = item.dataset.title || '';
            const description = item.dataset.description || '';
            const status = item.dataset.status || '';

            const matchesSearch = !searchTerm ||
                title.includes(searchTerm) ||
                description.includes(searchTerm);

            const matchesStatus = !statusValue || status === statusValue;

            if (matchesSearch && matchesStatus) {
                item.style.display = '';
                item.classList.remove('hidden');
                visibleCount++;
            } else {
                item.style.display = 'none';
                item.classList.add('hidden');
            }
        });

        // Show empty state if no results
        const container = document.getElementById('requestsContainer');
        let emptyState = container.querySelector('.empty-state');
        
        if (visibleCount === 0 && !emptyState) {
            emptyState = document.createElement('div');
            emptyState.className = 'empty-state';
            emptyState.innerHTML = `
                <div class="empty-icon">
                    <i class="fas fa-search"></i>
                </div>
                <h3>{{ __('No requests found') }}</h3>
                <p>{{ __('Try adjusting your search or filter criteria') }}</p>
            `;
            container.appendChild(emptyState);
        } else if (visibleCount > 0 && emptyState) {
            emptyState.remove();
        }

        // Sort visible items
        sortRequests(sortValue);
    }

    function sortRequests(sortType) {
        const container = document.getElementById('requestsContainer');
        const visibleItems = Array.from(requestItems).filter(item => !item.classList.contains('hidden'));

        visibleItems.sort((a, b) => {
            switch(sortType) {
                case 'oldest':
                    return 0; // Keep original order for oldest
                case 'most-funded':
                    const aAmount = parseFloat(a.dataset.amount || 0);
                    const bAmount = parseFloat(b.dataset.amount || 0);
                    return bAmount - aAmount;
                case 'closest-goal':
                    const aProgress = parseFloat(a.dataset.progress || 0);
                    const bProgress = parseFloat(b.dataset.progress || 0);
                    return bProgress - aProgress;
                case 'highest-goal':
                    // Would need goal amount in dataset
                    return 0;
                default: // newest
                    return 0; // Keep original order (newest first from server)
            }
        });

        // Reorder DOM elements
        visibleItems.forEach(item => {
            container.appendChild(item);
        });
    }

    // Event listeners with debounce
    let searchTimeout;
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(filterRequests, 300);
        });
    }

    if (statusFilter) {
        statusFilter.addEventListener('change', filterRequests);
    }

    if (sortBy) {
        sortBy.addEventListener('change', filterRequests);
    }
});
</script>
@endsection
