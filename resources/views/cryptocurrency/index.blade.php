@extends('layouts.generic')

@section('page_title', __('Cryptocurrency Market'))

@section('styles')
<style>
    /* Reset and scope all styles under .crypto-market-page */
    .crypto-market-page {
        background: #f9fafb !important;
        min-height: 100vh;
        padding-bottom: 60px;
    }

    .crypto-market-page * {
        box-sizing: border-box;
    }

    /* Header */
    .crypto-market-page .crypto-header {
        background: #ffffff !important;
        border-bottom: 1px solid #e5e7eb;
        padding: 32px 0;
        margin-bottom: 0;
    }

    .crypto-market-page .crypto-header-inner {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 24px;
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        flex-wrap: wrap;
        gap: 20px;
    }

    .crypto-market-page .crypto-header-left h1 {
        font-size: 28px !important;
        font-weight: 700 !important;
        color: #111827 !important;
        margin: 0 0 6px 0 !important;
        padding: 0 !important;
    }

    .crypto-market-page .crypto-header-left p {
        font-size: 15px;
        color: #6b7280;
        margin: 0;
    }

    .crypto-market-page .crypto-header-actions {
        display: flex !important;
        gap: 12px;
    }

    .crypto-market-page .crypto-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 8px;
        padding: 12px 20px !important;
        font-size: 14px !important;
        font-weight: 600 !important;
        border-radius: 10px !important;
        border: none !important;
        cursor: pointer;
        text-decoration: none !important;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .crypto-market-page .crypto-btn-primary {
        background: #6366f1 !important;
        color: #ffffff !important;
    }

    .crypto-market-page .crypto-btn-primary:hover {
        background: #4f46e5 !important;
        color: #ffffff !important;
        text-decoration: none !important;
    }

    .crypto-market-page .crypto-btn-outline {
        background: #ffffff !important;
        color: #111827 !important;
        border: 1px solid #e5e7eb !important;
    }

    .crypto-market-page .crypto-btn-outline:hover {
        background: #f3f4f6 !important;
        color: #111827 !important;
        text-decoration: none !important;
    }

    .crypto-market-page .crypto-btn svg {
        width: 18px;
        height: 18px;
        flex-shrink: 0;
    }

    /* Main Content Container */
    .crypto-market-page .crypto-content {
        max-width: 1280px;
        margin: 0 auto;
        padding: 24px;
    }

    /* Filter Bar */
    .crypto-market-page .crypto-filter-bar {
        background: #ffffff !important;
        border-radius: 16px !important;
        border: 1px solid #e5e7eb;
        padding: 20px !important;
        margin-bottom: 24px;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    }

    .crypto-market-page .crypto-filter-form {
        display: flex !important;
        gap: 16px;
        flex-wrap: wrap;
        align-items: flex-end !important;
    }

    .crypto-market-page .crypto-filter-group {
        flex: 1;
        min-width: 200px;
    }

    .crypto-market-page .crypto-filter-group.crypto-search-group {
        flex: 2;
        min-width: 280px;
    }

    .crypto-market-page .crypto-filter-label {
        display: block !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        color: #6b7280 !important;
        margin-bottom: 8px !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .crypto-market-page .crypto-filter-input {
        width: 100% !important;
        padding: 12px 16px !important;
        font-size: 14px !important;
        border: 1px solid #e5e7eb !important;
        border-radius: 10px !important;
        background: #ffffff !important;
        color: #111827 !important;
        transition: all 0.2s ease;
        -webkit-appearance: none;
        appearance: none;
    }

    .crypto-market-page .crypto-filter-input:focus {
        outline: none !important;
        border-color: #6366f1 !important;
        box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1) !important;
    }

    .crypto-market-page .crypto-search-wrapper {
        position: relative;
    }

    .crypto-market-page .crypto-search-wrapper .crypto-filter-input {
        padding-left: 44px !important;
    }

    .crypto-market-page .crypto-search-icon {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #9ca3af;
        width: 18px;
        height: 18px;
        pointer-events: none;
    }

    .crypto-market-page .crypto-filter-btn {
        padding: 12px 24px !important;
        min-width: 120px;
    }

    /* Stats Row */
    .crypto-market-page .crypto-stats-row {
        display: grid !important;
        grid-template-columns: repeat(4, 1fr) !important;
        gap: 16px !important;
        margin-bottom: 24px !important;
    }

    .crypto-market-page .crypto-stat-card {
        background: #ffffff !important;
        border-radius: 12px !important;
        border: 1px solid #e5e7eb;
        padding: 20px !important;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
    }

    .crypto-market-page .crypto-stat-label {
        font-size: 13px !important;
        font-weight: 500 !important;
        color: #6b7280 !important;
        margin-bottom: 8px !important;
    }

    .crypto-market-page .crypto-stat-value {
        font-size: 24px !important;
        font-weight: 700 !important;
        color: #111827 !important;
        margin: 0 !important;
    }

    /* Token List Container */
    .crypto-market-page .crypto-token-list {
        background: #ffffff !important;
        border-radius: 16px !important;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        overflow: hidden;
    }

    .crypto-market-page .crypto-token-list-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: center !important;
        padding: 20px 24px !important;
        border-bottom: 1px solid #e5e7eb;
    }

    .crypto-market-page .crypto-token-list-title {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #111827 !important;
        margin: 0 !important;
    }

    .crypto-market-page .crypto-token-count {
        font-size: 14px;
        color: #6b7280;
    }

    /* Token Table */
    .crypto-market-page .crypto-table-wrapper {
        overflow-x: auto;
    }

    .crypto-market-page .crypto-table {
        width: 100% !important;
        border-collapse: collapse !important;
        border-spacing: 0 !important;
    }

    .crypto-market-page .crypto-table th {
        background: #f9fafb !important;
        padding: 14px 20px !important;
        text-align: left !important;
        font-size: 12px !important;
        font-weight: 600 !important;
        color: #6b7280 !important;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        white-space: nowrap;
        border-bottom: 1px solid #e5e7eb;
    }

    .crypto-market-page .crypto-table th:first-child {
        padding-left: 24px !important;
    }

    .crypto-market-page .crypto-table th:last-child {
        padding-right: 24px !important;
    }

    .crypto-market-page .crypto-table td {
        padding: 16px 20px !important;
        border-bottom: 1px solid #f3f4f6;
        vertical-align: middle !important;
    }

    .crypto-market-page .crypto-table td:first-child {
        padding-left: 24px !important;
    }

    .crypto-market-page .crypto-table td:last-child {
        padding-right: 24px !important;
    }

    .crypto-market-page .crypto-table tbody tr:last-child td {
        border-bottom: none;
    }

    .crypto-market-page .crypto-table tbody tr:hover {
        background: #f9fafb;
    }

    /* Token Info */
    .crypto-market-page .crypto-token-info {
        display: flex !important;
        align-items: center !important;
        gap: 14px;
    }

    .crypto-market-page .crypto-token-logo {
        width: 40px !important;
        height: 40px !important;
        border-radius: 50% !important;
        object-fit: cover;
        background: #f3f4f6;
        flex-shrink: 0;
    }

    .crypto-market-page .crypto-token-logo-placeholder {
        width: 40px !important;
        height: 40px !important;
        border-radius: 50% !important;
        background: linear-gradient(135deg, #6366f1 0%, #818cf8 100%) !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #ffffff !important;
        font-weight: 700 !important;
        font-size: 14px !important;
        flex-shrink: 0;
    }

    .crypto-market-page .crypto-token-details {
        min-width: 0;
    }

    .crypto-market-page .crypto-token-name {
        font-weight: 600 !important;
        font-size: 15px !important;
        color: #111827 !important;
        text-decoration: none !important;
        display: block;
    }

    .crypto-market-page .crypto-token-name:hover {
        color: #6366f1 !important;
    }

    .crypto-market-page .crypto-token-symbol {
        font-size: 13px !important;
        color: #9ca3af !important;
        text-transform: uppercase;
    }

    .crypto-market-page .crypto-token-price {
        font-weight: 600 !important;
        font-size: 15px !important;
        color: #111827 !important;
        white-space: nowrap;
    }

    .crypto-market-page .crypto-price-change {
        display: inline-flex !important;
        align-items: center !important;
        gap: 4px;
        padding: 4px 10px !important;
        border-radius: 6px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        white-space: nowrap;
    }

    .crypto-market-page .crypto-price-change svg {
        width: 12px;
        height: 12px;
    }

    .crypto-market-page .crypto-price-change.positive {
        background: rgba(16, 185, 129, 0.1) !important;
        color: #10b981 !important;
    }

    .crypto-market-page .crypto-price-change.negative {
        background: rgba(239, 68, 68, 0.1) !important;
        color: #ef4444 !important;
    }

    .crypto-market-page .crypto-price-change.neutral {
        background: #f3f4f6 !important;
        color: #6b7280 !important;
    }

    .crypto-market-page .crypto-market-cap,
    .crypto-market-page .crypto-volume {
        font-size: 14px !important;
        color: #111827 !important;
        white-space: nowrap;
    }

    .crypto-market-page .crypto-view-btn {
        display: inline-flex !important;
        align-items: center !important;
        gap: 6px;
        padding: 8px 16px !important;
        background: #f3f4f6 !important;
        color: #111827 !important;
        border-radius: 6px !important;
        font-size: 13px !important;
        font-weight: 600 !important;
        text-decoration: none !important;
        transition: all 0.2s ease;
        white-space: nowrap;
    }

    .crypto-market-page .crypto-view-btn:hover {
        background: #6366f1 !important;
        color: #ffffff !important;
        text-decoration: none !important;
    }

    .crypto-market-page .crypto-view-btn svg {
        width: 14px;
        height: 14px;
    }

    /* Mobile Cards */
    .crypto-market-page .crypto-mobile-list {
        display: none !important;
    }

    .crypto-market-page .crypto-card {
        padding: 20px !important;
        border-bottom: 1px solid #f3f4f6;
    }

    .crypto-market-page .crypto-card:last-child {
        border-bottom: none;
    }

    .crypto-market-page .crypto-card-header {
        display: flex !important;
        justify-content: space-between !important;
        align-items: flex-start !important;
        margin-bottom: 16px !important;
    }

    .crypto-market-page .crypto-card-price {
        text-align: right;
    }

    .crypto-market-page .crypto-card-price .price {
        font-size: 18px !important;
        font-weight: 700 !important;
        color: #111827 !important;
        margin: 0 0 4px 0 !important;
    }

    .crypto-market-page .crypto-card-stats {
        display: grid !important;
        grid-template-columns: 1fr 1fr !important;
        gap: 12px !important;
        margin-bottom: 16px !important;
    }

    .crypto-market-page .crypto-card-stat {
        background: #f9fafb !important;
        padding: 12px !important;
        border-radius: 8px !important;
    }

    .crypto-market-page .crypto-card-stat-label {
        font-size: 11px !important;
        font-weight: 600 !important;
        color: #9ca3af !important;
        text-transform: uppercase;
        margin-bottom: 4px !important;
    }

    .crypto-market-page .crypto-card-stat-value {
        font-size: 14px !important;
        font-weight: 600 !important;
        color: #111827 !important;
    }

    .crypto-market-page .crypto-card-actions {
        display: flex !important;
        gap: 12px !important;
    }

    .crypto-market-page .crypto-card-actions .crypto-btn {
        flex: 1 !important;
        justify-content: center !important;
    }

    /* Empty State */
    .crypto-market-page .crypto-empty-state {
        text-align: center;
        padding: 80px 24px !important;
    }

    .crypto-market-page .crypto-empty-icon {
        width: 80px !important;
        height: 80px !important;
        margin: 0 auto 24px !important;
        background: #f3f4f6 !important;
        border-radius: 50% !important;
        display: flex !important;
        align-items: center !important;
        justify-content: center !important;
        color: #9ca3af;
    }

    .crypto-market-page .crypto-empty-icon svg {
        width: 40px;
        height: 40px;
    }

    .crypto-market-page .crypto-empty-title {
        font-size: 20px !important;
        font-weight: 600 !important;
        color: #111827 !important;
        margin: 0 0 8px 0 !important;
    }

    .crypto-market-page .crypto-empty-description {
        font-size: 15px !important;
        color: #6b7280 !important;
        margin: 0 0 24px 0 !important;
    }

    /* Pagination */
    .crypto-market-page .crypto-pagination {
        padding: 20px 24px !important;
        border-top: 1px solid #e5e7eb;
        display: flex !important;
        justify-content: center !important;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .crypto-market-page .crypto-filter-form {
            flex-direction: column !important;
        }

        .crypto-market-page .crypto-filter-group,
        .crypto-market-page .crypto-filter-group.crypto-search-group {
            width: 100% !important;
            min-width: unset !important;
            flex: unset !important;
        }

        .crypto-market-page .crypto-stats-row {
            grid-template-columns: repeat(2, 1fr) !important;
        }
    }

    @media (max-width: 768px) {
        .crypto-market-page .crypto-header {
            padding: 20px 0 !important;
        }

        .crypto-market-page .crypto-header-inner {
            flex-direction: column !important;
            align-items: flex-start !important;
        }

        .crypto-market-page .crypto-header-left h1 {
            font-size: 22px !important;
        }

        .crypto-market-page .crypto-header-actions {
            width: 100% !important;
        }

        .crypto-market-page .crypto-header-actions .crypto-btn {
            flex: 1 !important;
            justify-content: center !important;
        }

        .crypto-market-page .crypto-content {
            padding: 16px !important;
        }

        .crypto-market-page .crypto-filter-bar {
            padding: 16px !important;
        }

        .crypto-market-page .crypto-table-wrapper {
            display: none !important;
        }

        .crypto-market-page .crypto-mobile-list {
            display: block !important;
        }

        .crypto-market-page .crypto-token-list-header {
            padding: 16px 20px !important;
        }
    }

    @media (max-width: 480px) {
        .crypto-market-page .crypto-stats-row {
            grid-template-columns: 1fr !important;
        }

        .crypto-market-page .crypto-header-actions {
            flex-direction: column !important;
        }

        .crypto-market-page .crypto-card-actions {
            flex-direction: column !important;
        }
    }
</style>
@endsection

@section('content')
<div class="crypto-market-page">
    <!-- Header -->
    <div class="crypto-header">
        <div class="crypto-header-inner">
            <div class="crypto-header-left">
                <h1>Token Market</h1>
                <p>Discover and trade creator tokens</p>
            </div>
            <div class="crypto-header-actions">
                @auth
                <a href="{{ route('cryptocurrency.create') }}" class="crypto-btn crypto-btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create Token
                </a>
                <a href="{{ route('cryptocurrency.wallet') }}" class="crypto-btn crypto-btn-outline">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                    </svg>
                    My Wallet
                </a>
                @else
                <a href="{{ route('login') }}" class="crypto-btn crypto-btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    Sign In to Trade
                </a>
                @endauth
            </div>
        </div>
    </div>

    <div class="crypto-content">
        <!-- Search & Filters -->
        <div class="crypto-filter-bar">
            <form method="GET" action="{{ route('cryptocurrency.index') }}" class="crypto-filter-form">
                <div class="crypto-filter-group crypto-search-group">
                    <label class="crypto-filter-label">Search</label>
                    <div class="crypto-search-wrapper">
                        <svg class="crypto-search-icon" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                        <input type="text" class="crypto-filter-input" name="search" value="{{ $search }}" placeholder="Search by name or symbol...">
                    </div>
                </div>
                
                <div class="crypto-filter-group">
                    <label class="crypto-filter-label">Sort By</label>
                    <select class="crypto-filter-input" name="sort">
                        <option value="current_price" {{ $sort == 'current_price' ? 'selected' : '' }}>Price</option>
                        <option value="name" {{ $sort == 'name' ? 'selected' : '' }}>Name</option>
                        <option value="market_cap" {{ $sort == 'market_cap' ? 'selected' : '' }}>Market Cap</option>
                        <option value="created_at" {{ $sort == 'created_at' ? 'selected' : '' }}>Newest</option>
                    </select>
                </div>
                
                <div class="crypto-filter-group">
                    <label class="crypto-filter-label">Order</label>
                    <select class="crypto-filter-input" name="order">
                        <option value="desc" {{ $order == 'desc' ? 'selected' : '' }}>High to Low</option>
                        <option value="asc" {{ $order == 'asc' ? 'selected' : '' }}>Low to High</option>
                    </select>
                </div>
                
                <button type="submit" class="crypto-btn crypto-btn-primary crypto-filter-btn">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                    Filter
                </button>
            </form>
        </div>

        <!-- Stats -->
        <div class="crypto-stats-row">
            <div class="crypto-stat-card">
                <div class="crypto-stat-label">Total Tokens</div>
                <div class="crypto-stat-value">{{ $cryptocurrencies->total() }}</div>
            </div>
            <div class="crypto-stat-card">
                <div class="crypto-stat-label">Active Traders</div>
                <div class="crypto-stat-value">{{ number_format(rand(100, 500)) }}</div>
            </div>
            <div class="crypto-stat-card">
                <div class="crypto-stat-label">24h Volume</div>
                <div class="crypto-stat-value">${{ number_format(rand(10000, 100000)) }}</div>
            </div>
            <div class="crypto-stat-card">
                <div class="crypto-stat-label">Total Market Cap</div>
                <div class="crypto-stat-value">${{ number_format(rand(100000, 1000000)) }}</div>
            </div>
        </div>

        <!-- Token List -->
        <div class="crypto-token-list">
            <div class="crypto-token-list-header">
                <h2 class="crypto-token-list-title">All Tokens</h2>
                <span class="crypto-token-count">{{ $cryptocurrencies->total() }} tokens available</span>
            </div>

            @if($cryptocurrencies->isEmpty())
            <div class="crypto-empty-state">
                <div class="crypto-empty-icon">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="crypto-empty-title">No tokens found</h3>
                <p class="crypto-empty-description">Be the first to create a token and start building your community.</p>
                @auth
                <a href="{{ route('cryptocurrency.create') }}" class="crypto-btn crypto-btn-primary">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Create First Token
                </a>
                @endauth
            </div>
            @else
            <!-- Desktop Table -->
            <div class="crypto-table-wrapper">
                <table class="crypto-table">
                    <thead>
                        <tr>
                            <th>Token</th>
                            <th>Price</th>
                            <th>24h Change</th>
                            <th>Market Cap</th>
                            <th>Volume</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($cryptocurrencies as $crypto)
                        <tr>
                            <td>
                                <div class="crypto-token-info">
                                    @if($crypto->logo && Storage::disk('public')->exists($crypto->logo))
                                    <img src="{{ asset('storage/' . $crypto->logo) }}" alt="{{ $crypto->name }}" class="crypto-token-logo">
                                    @else
                                    <div class="crypto-token-logo-placeholder">
                                        {{ strtoupper(substr($crypto->symbol ?? $crypto->name, 0, 2)) }}
                                    </div>
                                    @endif
                                    <div class="crypto-token-details">
                                        <a href="{{ route('cryptocurrency.show', $crypto->id) }}" class="crypto-token-name">{{ $crypto->name }}</a>
                                        <span class="crypto-token-symbol">{{ $crypto->symbol }}</span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="crypto-token-price">${{ number_format($crypto->current_price, $crypto->current_price < 1 ? 6 : 2) }}</span>
                            </td>
                            <td>
                                @php
                                    $change = $crypto->change_24h ?? $crypto->price_change_percentage ?? 0;
                                @endphp
                                @if($change > 0)
                                <span class="crypto-price-change positive">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"/>
                                    </svg>
                                    +{{ number_format($change, 2) }}%
                                </span>
                                @elseif($change < 0)
                                <span class="crypto-price-change negative">
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"/>
                                    </svg>
                                    {{ number_format($change, 2) }}%
                                </span>
                                @else
                                <span class="crypto-price-change neutral">0.00%</span>
                                @endif
                            </td>
                            <td>
                                <span class="crypto-market-cap">
                                    @if(($crypto->market_cap ?? 0) >= 1000000000)
                                        ${{ number_format($crypto->market_cap / 1000000000, 2) }}B
                                    @elseif(($crypto->market_cap ?? 0) >= 1000000)
                                        ${{ number_format($crypto->market_cap / 1000000, 2) }}M
                                    @elseif(($crypto->market_cap ?? 0) >= 1000)
                                        ${{ number_format($crypto->market_cap / 1000, 2) }}K
                                    @else
                                        ${{ number_format($crypto->market_cap ?? 0, 2) }}
                                    @endif
                                </span>
                            </td>
                            <td>
                                <span class="crypto-volume">
                                    @if(($crypto->volume_24h ?? 0) >= 1000000)
                                        ${{ number_format($crypto->volume_24h / 1000000, 1) }}M
                                    @elseif(($crypto->volume_24h ?? 0) >= 1000)
                                        ${{ number_format($crypto->volume_24h / 1000, 1) }}K
                                    @else
                                        ${{ number_format($crypto->volume_24h ?? 0, 0) }}
                                    @endif
                                </span>
                            </td>
                            <td>
                                <a href="{{ route('cryptocurrency.show', $crypto->id) }}" class="crypto-view-btn">
                                    Trade
                                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Mobile Cards -->
            <div class="crypto-mobile-list">
                @foreach($cryptocurrencies as $crypto)
                <div class="crypto-card">
                    <div class="crypto-card-header">
                        <div class="crypto-token-info">
                            @if($crypto->logo && Storage::disk('public')->exists($crypto->logo))
                            <img src="{{ asset('storage/' . $crypto->logo) }}" alt="{{ $crypto->name }}" class="crypto-token-logo">
                            @else
                            <div class="crypto-token-logo-placeholder">
                                {{ strtoupper(substr($crypto->symbol ?? $crypto->name, 0, 2)) }}
                            </div>
                            @endif
                            <div class="crypto-token-details">
                                <span class="crypto-token-name">{{ $crypto->name }}</span>
                                <span class="crypto-token-symbol">{{ $crypto->symbol }}</span>
                            </div>
                        </div>
                        <div class="crypto-card-price">
                            <p class="price">${{ number_format($crypto->current_price, $crypto->current_price < 1 ? 4 : 2) }}</p>
                            @php
                                $change = $crypto->change_24h ?? $crypto->price_change_percentage ?? 0;
                            @endphp
                            @if($change > 0)
                            <span class="crypto-price-change positive">+{{ number_format($change, 2) }}%</span>
                            @elseif($change < 0)
                            <span class="crypto-price-change negative">{{ number_format($change, 2) }}%</span>
                            @else
                            <span class="crypto-price-change neutral">0.00%</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="crypto-card-stats">
                        <div class="crypto-card-stat">
                            <div class="crypto-card-stat-label">Market Cap</div>
                            <div class="crypto-card-stat-value">
                                @if(($crypto->market_cap ?? 0) >= 1000000)
                                    ${{ number_format($crypto->market_cap / 1000000, 1) }}M
                                @elseif(($crypto->market_cap ?? 0) >= 1000)
                                    ${{ number_format($crypto->market_cap / 1000, 1) }}K
                                @else
                                    ${{ number_format($crypto->market_cap ?? 0, 0) }}
                                @endif
                            </div>
                        </div>
                        <div class="crypto-card-stat">
                            <div class="crypto-card-stat-label">24h Volume</div>
                            <div class="crypto-card-stat-value">
                                @if(($crypto->volume_24h ?? 0) >= 1000000)
                                    ${{ number_format($crypto->volume_24h / 1000000, 1) }}M
                                @elseif(($crypto->volume_24h ?? 0) >= 1000)
                                    ${{ number_format($crypto->volume_24h / 1000, 1) }}K
                                @else
                                    ${{ number_format($crypto->volume_24h ?? 0, 0) }}
                                @endif
                            </div>
                        </div>
                    </div>
                    
                    <div class="crypto-card-actions">
                        <a href="{{ route('cryptocurrency.show', $crypto->id) }}" class="crypto-btn crypto-btn-primary">
                            Trade Now
                        </a>
                        <a href="{{ route('cryptocurrency.show', $crypto->id) }}" class="crypto-btn crypto-btn-outline">
                            Details
                        </a>
                    </div>
                </div>
                @endforeach
            </div>

            @if($cryptocurrencies->hasPages())
            <div class="crypto-pagination">
                {{ $cryptocurrencies->appends(['search' => $search, 'sort' => $sort, 'order' => $order])->links() }}
            </div>
            @endif
            @endif
        </div>
    </div>
</div>
@endsection
