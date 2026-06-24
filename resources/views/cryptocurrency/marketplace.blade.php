@extends('layouts.generic')

@section('page_title', 'Token Marketplace')

@section('styles')
<style>
    /* Base Styles */
    .marketplace-container {
        padding: 24px;
        max-width: 1280px;
        margin: 0 auto;
        min-height: 100vh;
    }
    
    /* Header Section */
    .marketplace-header {
        margin-bottom: 28px;
    }
    
    .marketplace-header h1 {
        font-size: 28px;
        font-weight: 700;
        color: #111827;
        margin: 0 0 8px 0;
    }
    
    .marketplace-header p {
        color: #6b7280;
        margin: 0;
        font-size: 15px;
    }
    
    /* Search & Filter Section */
    .search-filter-section {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        margin-bottom: 28px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
    }
    
    .search-wrapper {
        position: relative;
        margin-bottom: 16px;
    }
    
    .search-wrapper input {
        width: 100%;
        padding: 14px 16px 14px 48px;
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        font-size: 15px;
        transition: all 0.2s ease;
        background: #f9fafb;
    }
    
    .search-wrapper input:focus {
        outline: none;
        border-color: #6366f1;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
    }
    
    .search-wrapper .search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        width: 20px;
        height: 20px;
    }
    
    .search-wrapper button {
        position: absolute;
        right: 8px;
        top: 50%;
        transform: translateY(-50%);
        background: #6366f1;
        color: #fff;
        border: none;
        padding: 10px 20px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.2s ease;
    }
    
    .search-wrapper button:hover {
        background: #4f46e5;
    }
    
    .filter-tags {
        display: flex;
        gap: 8px;
        flex-wrap: wrap;
    }
    
    .filter-tag {
        padding: 8px 16px;
        background: #f3f4f6;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        color: #4b5563;
        cursor: pointer;
        transition: all 0.2s ease;
        border: none;
        text-decoration: none;
    }
    
    .filter-tag:hover, .filter-tag.active {
        background: #6366f1;
        color: #fff;
        text-decoration: none;
    }
    
    /* Section Headers */
    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    
    .section-header h2 {
        font-size: 18px;
        font-weight: 700;
        color: #111827;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .section-header h2 svg {
        width: 20px;
        height: 20px;
        color: #6366f1;
    }
    
    .section-header span {
        font-size: 14px;
        color: #6b7280;
    }
    
    /* Token Cards Grid */
    .tokens-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 16px;
        margin-bottom: 32px;
    }
    
    .token-card {
        background: #fff;
        border-radius: 16px;
        padding: 20px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
        text-decoration: none;
        color: inherit;
        display: block;
    }
    
    .token-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border-color: #6366f1;
        text-decoration: none;
        color: inherit;
    }
    
    .token-header {
        display: flex;
        align-items: center;
        gap: 14px;
        margin-bottom: 16px;
    }
    
    .token-logo {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: #fff;
        font-weight: 700;
        font-size: 16px;
        flex-shrink: 0;
    }
    
    .token-logo img {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        object-fit: cover;
    }
    
    .token-info h3 {
        font-size: 16px;
        font-weight: 600;
        color: #111827;
        margin: 0 0 4px 0;
    }
    
    .token-info .symbol {
        font-size: 13px;
        color: #6b7280;
        font-weight: 500;
        text-transform: uppercase;
    }
    
    .token-price-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
    }
    
    .token-price {
        font-size: 22px;
        font-weight: 700;
        color: #111827;
    }
    
    .token-change {
        padding: 4px 10px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
    }
    
    .token-change.positive {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }
    
    .token-change.negative {
        background: rgba(239, 68, 68, 0.1);
        color: #ef4444;
    }
    
    .token-stats {
        display: flex;
        gap: 16px;
        padding-top: 16px;
        border-top: 1px solid #f3f4f6;
    }
    
    .token-stat {
        flex: 1;
    }
    
    .token-stat label {
        font-size: 11px;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 4px;
        font-weight: 600;
    }
    
    .token-stat span {
        font-size: 14px;
        font-weight: 600;
        color: #111827;
    }
    
    .token-actions {
        display: flex;
        gap: 10px;
        margin-top: 16px;
    }
    
    .token-btn {
        flex: 1;
        padding: 12px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 600;
        text-align: center;
        text-decoration: none;
        transition: all 0.2s ease;
        border: none;
        cursor: pointer;
    }
    
    .token-btn.primary {
        background: #6366f1;
        color: #fff;
    }
    
    .token-btn.primary:hover {
        background: #4f46e5;
        color: #fff;
        text-decoration: none;
    }
    
    .token-btn.secondary {
        background: #f3f4f6;
        color: #374151;
    }
    
    .token-btn.secondary:hover {
        background: #e5e7eb;
        color: #374151;
        text-decoration: none;
    }
    
    /* Creator Info */
    .creator-info {
        display: flex;
        align-items: center;
        gap: 8px;
        margin-top: 12px;
        padding-top: 12px;
        border-top: 1px solid #f3f4f6;
    }
    
    .creator-avatar {
        width: 24px;
        height: 24px;
        border-radius: 50%;
        object-fit: cover;
        background: #e5e7eb;
    }
    
    .creator-name {
        font-size: 13px;
        color: #6b7280;
    }
    
    /* CTA Section */
    .cta-section {
        background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%);
        border-radius: 20px;
        padding: 40px 24px;
        text-align: center;
        color: #fff;
        margin-top: 32px;
    }
    
    .cta-section h2 {
        font-size: 24px;
        font-weight: 700;
        margin: 0 0 12px 0;
    }
    
    .cta-section p {
        font-size: 15px;
        opacity: 0.9;
        margin: 0 0 24px 0;
    }
    
    .cta-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: #fff;
        color: #6366f1;
        padding: 14px 28px;
        border-radius: 12px;
        font-weight: 700;
        font-size: 15px;
        text-decoration: none;
        transition: all 0.2s ease;
    }
    
    .cta-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        color: #6366f1;
        text-decoration: none;
    }
    
    .cta-btn svg {
        width: 18px;
        height: 18px;
    }
    
    /* Pagination */
    .pagination-wrapper {
        display: flex;
        justify-content: center;
        margin-top: 24px;
    }
    
    .pagination-wrapper .pagination {
        display: flex;
        gap: 8px;
        list-style: none;
        padding: 0;
        margin: 0;
    }
    
    .pagination-wrapper .page-item .page-link {
        padding: 10px 16px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        color: #4b5563;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.2s ease;
    }
    
    .pagination-wrapper .page-item.active .page-link {
        background: #6366f1;
        border-color: #6366f1;
        color: #fff;
    }
    
    .pagination-wrapper .page-item .page-link:hover {
        background: #f3f4f6;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 80px 24px;
        background: #f9fafb;
        border-radius: 16px;
    }
    
    .empty-state .empty-icon {
        width: 64px;
        height: 64px;
        color: #9ca3af;
        margin: 0 auto 16px;
    }
    
    .empty-state h3 {
        font-size: 18px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 8px;
    }
    
    .empty-state p {
        color: #6b7280;
        margin-bottom: 24px;
    }
    
    /* Mobile Responsive */
    @media (max-width: 768px) {
        .marketplace-container {
            padding: 16px;
        }
        
        .marketplace-header h1 {
            font-size: 22px;
        }
        
        .search-wrapper input {
            padding: 12px 12px 12px 44px;
            font-size: 14px;
        }
        
        .search-wrapper button {
            padding: 8px 14px;
            font-size: 13px;
        }
        
        .tokens-grid {
            grid-template-columns: 1fr;
            gap: 12px;
        }
        
        .token-card {
            padding: 16px;
        }
        
        .token-logo {
            width: 44px;
            height: 44px;
            font-size: 14px;
        }
        
        .token-info h3 {
            font-size: 15px;
        }
        
        .token-price {
            font-size: 18px;
        }
        
        .cta-section {
            padding: 32px 20px;
        }
        
        .cta-section h2 {
            font-size: 20px;
        }
        
        .filter-tags {
            overflow-x: auto;
            flex-wrap: nowrap;
            padding-bottom: 8px;
            -webkit-overflow-scrolling: touch;
        }
        
        .filter-tag {
            flex-shrink: 0;
        }
    }
    
    @media (max-width: 480px) {
        .marketplace-header h1 {
            font-size: 20px;
        }
        
        .section-header h2 {
            font-size: 16px;
        }
        
        .token-actions {
            flex-direction: column;
        }
    }
</style>
@endsection

@section('content')
<div class="marketplace-container">
    <!-- Header -->
    <div class="marketplace-header">
        <h1>Token Marketplace</h1>
        <p>Discover and invest in creator tokens</p>
    </div>
    
    <!-- Search & Filter -->
    <div class="search-filter-section">
        <form action="{{ route('cryptocurrency.marketplace') }}" method="GET">
            <div class="search-wrapper">
                <svg class="search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                </svg>
                <input type="text" name="search" placeholder="Search tokens by name or symbol..." value="{{ request('search') }}">
                <button type="submit">Search</button>
            </div>
        </form>
        <div class="filter-tags">
            <a href="{{ route('cryptocurrency.marketplace') }}" class="filter-tag {{ !request('sort') ? 'active' : '' }}">All</a>
            <a href="{{ route('cryptocurrency.marketplace', ['sort' => 'current_price', 'order' => 'desc']) }}" class="filter-tag {{ request('sort') == 'current_price' && request('order') == 'desc' ? 'active' : '' }}">Price: High</a>
            <a href="{{ route('cryptocurrency.marketplace', ['sort' => 'current_price', 'order' => 'asc']) }}" class="filter-tag {{ request('sort') == 'current_price' && request('order') == 'asc' ? 'active' : '' }}">Price: Low</a>
            <a href="{{ route('cryptocurrency.marketplace', ['sort' => 'market_cap', 'order' => 'desc']) }}" class="filter-tag {{ request('sort') == 'market_cap' ? 'active' : '' }}">Market Cap</a>
            <a href="{{ route('cryptocurrency.marketplace', ['sort' => 'created_at', 'order' => 'desc']) }}" class="filter-tag {{ request('sort') == 'created_at' ? 'active' : '' }}">Newest</a>
        </div>
    </div>
    
    <!-- Trending Tokens -->
    @if($trending->count() > 0 && !request('search'))
    <div class="section-header">
        <h2>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
            </svg>
            Trending Tokens
        </h2>
    </div>
    <div class="tokens-grid">
        @foreach($trending->take(3) as $crypto)
        <a href="{{ route('cryptocurrency.show', $crypto->id) }}" class="token-card">
            <div class="token-header">
                <div class="token-logo">
                    @if($crypto->logo)
                        <img src="{{ asset('storage/' . $crypto->logo) }}" alt="{{ $crypto->name }}">
                    @else
                        {{ strtoupper(substr($crypto->symbol ?? $crypto->name, 0, 2)) }}
                    @endif
                </div>
                <div class="token-info">
                    <h3>{{ $crypto->name }}</h3>
                    <span class="symbol">{{ $crypto->symbol }}</span>
                </div>
            </div>
            <div class="token-price-row">
                <span class="token-price">${{ number_format($crypto->current_price, $crypto->current_price < 1 ? 4 : 2) }}</span>
                <span class="token-change {{ ($crypto->price_change_percentage ?? 0) >= 0 ? 'positive' : 'negative' }}">
                    {{ ($crypto->price_change_percentage ?? 0) >= 0 ? '+' : '' }}{{ number_format($crypto->price_change_percentage ?? 0, 2) }}%
                </span>
            </div>
            <div class="token-stats">
                <div class="token-stat">
                    <label>Market Cap</label>
                    <span>${{ number_format($crypto->market_cap ?? 0, 0) }}</span>
                </div>
                <div class="token-stat">
                    <label>Supply</label>
                    <span>{{ number_format($crypto->total_supply ?? 0, 0) }}</span>
                </div>
            </div>
            @if($crypto->creator)
            <div class="creator-info">
                <img src="{{ $crypto->creator->avatar ?? asset('img/default-avatar.png') }}" alt="" class="creator-avatar">
                <span class="creator-name">by {{ $crypto->creator->name }}</span>
            </div>
            @endif
            <div class="token-actions" onclick="event.stopPropagation();">
                <a href="{{ route('cryptocurrency.buy.form', $crypto->id) }}" class="token-btn primary">Buy</a>
                <a href="{{ route('cryptocurrency.show', $crypto->id) }}" class="token-btn secondary">Details</a>
            </div>
        </a>
        @endforeach
    </div>
    @endif
    
    <!-- All Tokens -->
    <div class="section-header">
        <h2>
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            All Tokens
        </h2>
        <span>{{ $cryptocurrencies->total() }} tokens</span>
    </div>
    
    @if($cryptocurrencies->count() > 0)
    <div class="tokens-grid">
        @foreach($cryptocurrencies as $crypto)
        <a href="{{ route('cryptocurrency.show', $crypto->id) }}" class="token-card">
            <div class="token-header">
                <div class="token-logo">
                    @if($crypto->logo)
                        <img src="{{ asset('storage/' . $crypto->logo) }}" alt="{{ $crypto->name }}">
                    @else
                        {{ strtoupper(substr($crypto->symbol ?? $crypto->name, 0, 2)) }}
                    @endif
                </div>
                <div class="token-info">
                    <h3>{{ $crypto->name }}</h3>
                    <span class="symbol">{{ $crypto->symbol }}</span>
                </div>
            </div>
            <div class="token-price-row">
                <span class="token-price">${{ number_format($crypto->current_price, $crypto->current_price < 1 ? 4 : 2) }}</span>
                <span class="token-change {{ ($crypto->price_change_percentage ?? 0) >= 0 ? 'positive' : 'negative' }}">
                    {{ ($crypto->price_change_percentage ?? 0) >= 0 ? '+' : '' }}{{ number_format($crypto->price_change_percentage ?? 0, 2) }}%
                </span>
            </div>
            <div class="token-stats">
                <div class="token-stat">
                    <label>Market Cap</label>
                    <span>${{ number_format($crypto->market_cap ?? 0, 0) }}</span>
                </div>
                <div class="token-stat">
                    <label>Supply</label>
                    <span>{{ number_format($crypto->total_supply ?? 0, 0) }}</span>
                </div>
            </div>
            @if($crypto->creator)
            <div class="creator-info">
                <img src="{{ $crypto->creator->avatar ?? asset('img/default-avatar.png') }}" alt="" class="creator-avatar">
                <span class="creator-name">by {{ $crypto->creator->name }}</span>
            </div>
            @endif
            <div class="token-actions" onclick="event.stopPropagation();">
                <a href="{{ route('cryptocurrency.buy.form', $crypto->id) }}" class="token-btn primary">Buy</a>
                <a href="{{ route('cryptocurrency.show', $crypto->id) }}" class="token-btn secondary">Details</a>
            </div>
        </a>
        @endforeach
    </div>
    
    <div class="pagination-wrapper">
        {{ $cryptocurrencies->appends(request()->query())->links() }}
    </div>
    @else
    <div class="empty-state">
        <svg class="empty-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <h3>No tokens found</h3>
        <p>{{ request('search') ? 'Try a different search term' : 'Be the first to create a token!' }}</p>
    </div>
    @endif
    
    <!-- CTA Section -->
    <div class="cta-section">
        <h2>Create Your Own Token</h2>
        <p>Launch your cryptocurrency and let your fans invest in your success</p>
        <a href="{{ route('cryptocurrency.create') }}" class="cta-btn">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Get Started
        </a>
    </div>
</div>
@endsection
