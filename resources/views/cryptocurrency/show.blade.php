@extends('layouts.generic')

@section('content')
<div class="crypto-detail-page">
    <div class="container-fluid px-4 py-4">
        <!-- Modern Hero Header -->
        <div class="crypto-hero-section mb-4">
            <div class="crypto-hero-card">
                <div class="crypto-hero-gradient"></div>
                <div class="crypto-hero-content">
                    <div class="row align-items-center">
                        <div class="col-auto">
                            <div class="crypto-logo-wrapper">
                                @if($cryptocurrency->logo && Storage::disk('public')->exists($cryptocurrency->logo))
                                    <img src="{{ asset('storage/' . $cryptocurrency->logo) }}" 
                                         alt="{{ $cryptocurrency->name }} Logo" 
                                         class="crypto-logo-img">
                                @else
                                    <div class="crypto-logo-placeholder">
                                        {{ substr($cryptocurrency->symbol, 0, 2) }}
                                    </div>
                                @endif
                                @if($cryptocurrency->is_verified)
                                    <div class="crypto-verified-badge">
                                        <i class="fas fa-check-circle"></i>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="col">
                            <div class="crypto-title-section">
                                <h1 class="crypto-name">{{ $cryptocurrency->name }}</h1>
                                <div class="crypto-symbol-badge">{{ $cryptocurrency->symbol }}</div>
                            </div>
                            <div class="crypto-price-section">
                                <div class="crypto-current-price">${{ number_format($cryptocurrency->current_price, 8) }}</div>
                                @php
                                    $change = $cryptocurrency->change_24h ?? 0;
                                @endphp
                                <div class="crypto-price-change {{ $change >= 0 ? 'positive' : 'negative' }}">
                                    <i class="fas fa-{{ $change >= 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                                    {{ $change >= 0 ? '+' : '' }}{{ number_format($change, 2) }}%
                                    <span class="change-label">24h</span>
                                </div>
                            </div>
                            <div class="crypto-action-buttons">
                                <a href="{{ route('cryptocurrency.buy.form', $cryptocurrency->id) }}" 
                                   class="crypto-btn crypto-btn-buy">
                                    <i class="fas fa-arrow-up"></i>
                                    <span>Buy</span>
                                </a>
                                @if($wallet && $wallet->balance > 0)
                                    <a href="{{ route('cryptocurrency.sell.form', $cryptocurrency->id) }}" 
                                       class="crypto-btn crypto-btn-sell">
                                        <i class="fas fa-arrow-down"></i>
                                        <span>Sell</span>
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modern Wallet Section -->
        @if($wallet && $wallet->balance > 0)
        <div class="crypto-wallet-section mb-4">
            <div class="crypto-wallet-card">
                <div class="crypto-wallet-header">
                    <i class="fas fa-wallet"></i>
                    <span>Your Portfolio</span>
                </div>
                <div class="crypto-wallet-grid">
                    <div class="wallet-stat-item">
                        <div class="wallet-stat-icon">
                            <i class="fas fa-coins"></i>
                        </div>
                        <div class="wallet-stat-content">
                            <div class="wallet-stat-value">{{ number_format($wallet->balance, 8) }}</div>
                            <div class="wallet-stat-label">{{ $cryptocurrency->symbol }} Balance</div>
                        </div>
                    </div>
                    <div class="wallet-stat-item">
                        <div class="wallet-stat-icon">
                            <i class="fas fa-dollar-sign"></i>
                        </div>
                        <div class="wallet-stat-content">
                            <div class="wallet-stat-value">${{ number_format($wallet->balance * $cryptocurrency->current_price, 2) }}</div>
                            <div class="wallet-stat-label">USD Value</div>
                        </div>
                    </div>
                    <div class="wallet-stat-item">
                        <div class="wallet-stat-icon">
                            <i class="fas fa-chart-pie"></i>
                        </div>
                        <div class="wallet-stat-content">
                            <div class="wallet-stat-value">{{ number_format(($wallet->balance / $cryptocurrency->total_supply) * 100, 2) }}%</div>
                            <div class="wallet-stat-label">Of Total Supply</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Modern Market Stats Grid -->
        <div class="crypto-section-header">
            <h2>Market Statistics</h2>
        </div>
        <div class="crypto-stats-grid mb-4">
            <div class="crypto-stat-card">
                <div class="stat-card-icon market-cap">
                    <i class="fas fa-chart-line"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-label">Market Cap</div>
                    <div class="stat-card-value">${{ number_format($cryptocurrency->market_cap, 2) }}</div>
                </div>
            </div>
            
            <div class="crypto-stat-card">
                <div class="stat-card-icon volume">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-label">24h Volume</div>
                    <div class="stat-card-value">${{ number_format($cryptocurrency->volume_24h, 2) }}</div>
                </div>
            </div>
            
            <div class="crypto-stat-card">
                <div class="stat-card-icon transactions">
                    <i class="fas fa-receipt"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-label">24h Transactions</div>
                    <div class="stat-card-value">{{ $cryptocurrency->transactions()->whereDate('created_at', today())->count() }}</div>
                </div>
            </div>
            
            <div class="crypto-stat-card">
                <div class="stat-card-icon supply">
                    <i class="fas fa-database"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-label">Total Supply</div>
                    <div class="stat-card-value">{{ number_format($cryptocurrency->total_supply, 0) }}</div>
                    <div class="stat-card-subtitle">{{ $cryptocurrency->symbol }}</div>
                </div>
            </div>
            
            <div class="crypto-stat-card">
                <div class="stat-card-icon available">
                    <i class="fas fa-box-open"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-label">Available</div>
                    <div class="stat-card-value">{{ number_format($cryptocurrency->available_supply, 0) }}</div>
                    <div class="stat-card-subtitle">{{ $cryptocurrency->symbol }}</div>
                </div>
            </div>
            
            <div class="crypto-stat-card">
                <div class="stat-card-icon circulating">
                    <i class="fas fa-sync-alt"></i>
                </div>
                <div class="stat-card-content">
                    <div class="stat-card-label">Circulating</div>
                    <div class="stat-card-value">{{ number_format($cryptocurrency->circulating_supply, 0) }}</div>
                    <div class="stat-card-subtitle">{{ $cryptocurrency->symbol }}</div>
                </div>
            </div>
        </div>

        <!-- Token Details & Fee Structure -->
        <div class="row g-4 mb-4">
            <div class="col-lg-6">
                <div class="crypto-info-card">
                    <div class="crypto-info-header">
                        <i class="fas fa-info-circle"></i>
                        <span>Token Details</span>
                    </div>
                    <div class="crypto-info-body">
                        <div class="info-row">
                            <div class="info-item">
                                <div class="info-icon"><i class="fas fa-network-wired"></i></div>
                                <div class="info-content">
                                    <div class="info-label">Network</div>
                                    <div class="info-value">{{ ucfirst($cryptocurrency->blockchain_network) }}</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon"><i class="fas fa-tag"></i></div>
                                <div class="info-content">
                                    <div class="info-label">Type</div>
                                    <div class="info-value">{{ ucfirst($cryptocurrency->token_type) }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="info-row">
                            <div class="info-item">
                                <div class="info-icon"><i class="fas fa-rocket"></i></div>
                                <div class="info-content">
                                    <div class="info-label">Initial Price</div>
                                    <div class="info-value">${{ number_format($cryptocurrency->initial_price, 8) }}</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <div class="info-icon"><i class="fas fa-calendar-alt"></i></div>
                                <div class="info-content">
                                    <div class="info-label">Created</div>
                                    <div class="info-value">{{ $cryptocurrency->created_at->format('M d, Y') }}</div>
                                </div>
                            </div>
                        </div>
                        <div class="info-features">
                            <div class="info-label mb-2">Features</div>
                            <div class="feature-badges">
                                @if($cryptocurrency->transferable)
                                    <span class="feature-badge transferable">
                                        <i class="fas fa-exchange-alt"></i> Transferable
                                    </span>
                                @endif
                                @if($cryptocurrency->enable_burning)
                                    <span class="feature-badge burnable">
                                        <i class="fas fa-fire"></i> Burnable
                                    </span>
                                @endif
                                @if($cryptocurrency->enable_minting)
                                    <span class="feature-badge mintable">
                                        <i class="fas fa-plus-circle"></i> Mintable
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-6">
                <div class="crypto-info-card">
                    <div class="crypto-info-header">
                        <i class="fas fa-percentage"></i>
                        <span>Fee Structure</span>
                    </div>
                    <div class="crypto-info-body">
                        <div class="fee-item">
                            <div class="fee-label">
                                <i class="fas fa-user-tie"></i>
                                <span>Creator Fee</span>
                            </div>
                            <div class="fee-value">{{ number_format($cryptocurrency->creator_fee_percentage, 2) }}%</div>
                        </div>
                        <div class="fee-item">
                            <div class="fee-label">
                                <i class="fas fa-building"></i>
                                <span>Platform Fee</span>
                            </div>
                            <div class="fee-value">{{ number_format($cryptocurrency->platform_fee_percentage, 2) }}%</div>
                        </div>
                        <div class="fee-item">
                            <div class="fee-label">
                                <i class="fas fa-water"></i>
                                <span>Liquidity Pool</span>
                            </div>
                            <div class="fee-value">{{ number_format($cryptocurrency->liquidity_pool_percentage, 2) }}%</div>
                        </div>
                        <div class="fee-divider"></div>
                        <div class="info-item-full">
                            <div class="info-icon"><i class="fas fa-user-circle"></i></div>
                            <div class="info-content">
                                <div class="info-label">Creator</div>
                                <div class="info-value">{{ $cryptocurrency->creator->name ?? 'Unknown' }}</div>
                            </div>
                        </div>
                        @if($cryptocurrency->contract_address)
                        <div class="info-item-full">
                            <div class="info-icon"><i class="fas fa-file-contract"></i></div>
                            <div class="info-content">
                                <div class="info-label">Contract Address</div>
                                <div class="contract-address">{{ substr($cryptocurrency->contract_address, 0, 8) }}...{{ substr($cryptocurrency->contract_address, -6) }}</div>
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- About Section -->
        <div class="crypto-about-section mb-4">
            <div class="crypto-about-card">
                <div class="crypto-about-header">
                    <h3>About {{ $cryptocurrency->name }}</h3>
                </div>
                <div class="crypto-about-body">
                    <p class="crypto-description">{{ $cryptocurrency->description }}</p>
                    
                    @if($cryptocurrency->website || $cryptocurrency->whitepaper)
                        <div class="crypto-links">
                            @if($cryptocurrency->website)
                                <a href="{{ $cryptocurrency->website }}" target="_blank" class="crypto-link website">
                                    <i class="fas fa-globe"></i>
                                    <span>Visit Website</span>
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            @endif
                            @if($cryptocurrency->whitepaper)
                                <a href="{{ $cryptocurrency->whitepaper }}" target="_blank" class="crypto-link whitepaper">
                                    <i class="fas fa-file-pdf"></i>
                                    <span>Read Whitepaper</span>
                                    <i class="fas fa-external-link-alt"></i>
                                </a>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Recent Transactions -->
        <div class="crypto-transactions-section">
            <div class="crypto-transactions-card">
                <div class="crypto-transactions-header">
                    <h3>Recent Transactions</h3>
                    <button class="crypto-refresh-btn" onclick="refreshTransactions()">
                        <i class="fas fa-sync-alt"></i>
                        <span>Refresh</span>
                    </button>
                </div>
                <div class="crypto-transactions-body">
                    <div class="transactions-table-wrapper" id="transactions-table">
                        @forelse($cryptocurrency->transactions()->latest()->take(10)->get() as $transaction)
                            <div class="transaction-row">
                                <div class="transaction-type">
                                    <span class="transaction-badge {{ $transaction->type }}">
                                        <i class="fas fa-{{ $transaction->type == 'buy' ? 'arrow-up' : ($transaction->type == 'sell' ? 'arrow-down' : 'exchange-alt') }}"></i>
                                        {{ ucfirst($transaction->type) }}
                                    </span>
                                </div>
                                <div class="transaction-amount">
                                    <div class="amount-value">{{ number_format($transaction->amount, 8) }}</div>
                                    <div class="amount-symbol">{{ $cryptocurrency->symbol }}</div>
                                </div>
                                <div class="transaction-price">
                                    <div class="price-label">Price</div>
                                    <div class="price-value">${{ number_format($transaction->price_per_token, 8) }}</div>
                                </div>
                                <div class="transaction-total">
                                    <div class="total-label">Total</div>
                                    <div class="total-value">${{ number_format($transaction->total_price, 2) }}</div>
                                </div>
                                <div class="transaction-date">
                                    <i class="fas fa-clock"></i>
                                    <span>{{ $transaction->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @empty
                            <div class="no-transactions">
                                <div class="no-transactions-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <h4>No Transactions Yet</h4>
                                <p>Be the first to trade this token!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Modern Cryptocurrency Detail Page Styles */
:root {
    --primary-color: #6366f1;
    --primary-dark: #4f46e5;
    --secondary-color: #8b5cf6;
    --success-color: #10b981;
    --success-dark: #059669;
    --danger-color: #ef4444;
    --danger-dark: #dc2626;
    --warning-color: #f59e0b;
    --info-color: #3b82f6;
    --dark-bg: #1e293b;
    --light-bg: #f8fafc;
    --card-bg: #ffffff;
    --border-color: #e2e8f0;
    --text-primary: #1e293b;
    --text-secondary: #64748b;
    --text-muted: #94a3b8;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --shadow-xl: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
}

.crypto-detail-page {
    background: linear-gradient(135deg, #667eea10 0%, #764ba210 100%);
    min-height: 100vh;
    padding-bottom: 2rem;
}

/* Hero Section */
.crypto-hero-section {
    margin-bottom: 2rem;
}

.crypto-hero-card {
    position: relative;
    background: var(--card-bg);
    border-radius: 24px;
    overflow: hidden;
    box-shadow: var(--shadow-xl);
}

.crypto-hero-gradient {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 180px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    opacity: 0.1;
}

.crypto-hero-content {
    position: relative;
    padding: 2.5rem;
}

.crypto-logo-wrapper {
    position: relative;
    width: 100px;
    height: 100px;
}

.crypto-logo-img {
    width: 100px;
    height: 100px;
    border-radius: 20px;
    object-fit: cover;
    border: 4px solid var(--card-bg);
    box-shadow: var(--shadow-lg);
}

.crypto-logo-placeholder {
    width: 100px;
    height: 100px;
    border-radius: 20px;
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2.5rem;
    font-weight: 700;
    color: white;
    border: 4px solid var(--card-bg);
    box-shadow: var(--shadow-lg);
}

.crypto-verified-badge {
    position: absolute;
    bottom: -5px;
    right: -5px;
    width: 32px;
    height: 32px;
    background: var(--success-color);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 16px;
    border: 3px solid var(--card-bg);
    box-shadow: var(--shadow-md);
}

.crypto-title-section {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
}

.crypto-name {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin: 0;
}

.crypto-symbol-badge {
    display: inline-block;
    padding: 0.5rem 1rem;
    background: linear-gradient(135deg, var(--primary-color)15, var(--secondary-color)15);
    color: var(--primary-color);
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    border: 2px solid var(--primary-color)30;
}

.crypto-price-section {
    display: flex;
    align-items: center;
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.crypto-current-price {
    font-size: 2.5rem;
    font-weight: 700;
    color: var(--text-primary);
}

.crypto-price-change {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1.25rem;
    border-radius: 12px;
    font-size: 1.125rem;
    font-weight: 600;
}

.crypto-price-change.positive {
    background: var(--success-color)15;
    color: var(--success-color);
}

.crypto-price-change.negative {
    background: var(--danger-color)15;
    color: var(--danger-color);
}

.crypto-price-change .change-label {
    font-size: 0.875rem;
    opacity: 0.7;
    margin-left: 0.25rem;
}

.crypto-action-buttons {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.crypto-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1rem 2rem;
    border-radius: 14px;
    font-size: 1rem;
    font-weight: 600;
    text-decoration: none;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
}

.crypto-btn i {
    font-size: 1.125rem;
}

.crypto-btn-buy {
    background: linear-gradient(135deg, var(--success-color), var(--success-dark));
    color: white;
    box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
}

.crypto-btn-buy:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(16, 185, 129, 0.4);
    color: white;
}

.crypto-btn-sell {
    background: linear-gradient(135deg, var(--danger-color), var(--danger-dark));
    color: white;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
}

.crypto-btn-sell:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(239, 68, 68, 0.4);
    color: white;
}

/* Wallet Section */
.crypto-wallet-section {
    margin-bottom: 2rem;
}

.crypto-wallet-card {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 20px;
    padding: 2rem;
    box-shadow: var(--shadow-xl);
    color: white;
}

.crypto-wallet-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
}

.crypto-wallet-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1.5rem;
}

.wallet-stat-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1.5rem;
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(10px);
    border-radius: 16px;
    border: 1px solid rgba(255, 255, 255, 0.2);
}

.wallet-stat-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.wallet-stat-content {
    flex: 1;
}

.wallet-stat-value {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.25rem;
}

.wallet-stat-label {
    font-size: 0.875rem;
    opacity: 0.9;
}

/* Section Header */
.crypto-section-header {
    margin-bottom: 1.5rem;
}

.crypto-section-header h2 {
    font-size: 1.75rem;
    font-weight: 700;
    color: var(--text-primary);
}

/* Stats Grid */
.crypto-stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.25rem;
    margin-bottom: 2rem;
}

.crypto-stat-card {
    background: var(--card-bg);
    border-radius: 16px;
    padding: 1.5rem;
    box-shadow: var(--shadow-md);
    transition: all 0.3s ease;
    border: 1px solid var(--border-color);
}

.crypto-stat-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-xl);
}

.stat-card-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1rem;
    font-size: 1.25rem;
    color: white;
}

.stat-card-icon.market-cap {
    background: linear-gradient(135deg, #3b82f6, #2563eb);
}

.stat-card-icon.volume {
    background: linear-gradient(135deg, #8b5cf6, #7c3aed);
}

.stat-card-icon.transactions {
    background: linear-gradient(135deg, #f59e0b, #d97706);
}

.stat-card-icon.supply {
    background: linear-gradient(135deg, #10b981, #059669);
}

.stat-card-icon.available {
    background: linear-gradient(135deg, #06b6d4, #0891b2);
}

.stat-card-icon.circulating {
    background: linear-gradient(135deg, #ec4899, #db2777);
}

.stat-card-content {
    flex: 1;
}

.stat-card-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.5rem;
    font-weight: 500;
}

.stat-card-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.stat-card-subtitle {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-top: 0.25rem;
}

/* Info Cards */
.crypto-info-card {
    background: var(--card-bg);
    border-radius: 20px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
    border: 1px solid var(--border-color);
    height: 100%;
}

.crypto-info-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 1.5rem;
    background: linear-gradient(135deg, var(--primary-color)10, var(--secondary-color)10);
    border-bottom: 1px solid var(--border-color);
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--text-primary);
}

.crypto-info-body {
    padding: 1.5rem;
}

.info-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1.5rem;
    margin-bottom: 1.5rem;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
}

.info-icon {
    width: 40px;
    height: 40px;
    background: linear-gradient(135deg, var(--primary-color)15, var(--secondary-color)15);
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-color);
    flex-shrink: 0;
}

.info-content {
    flex: 1;
}

.info-label {
    font-size: 0.875rem;
    color: var(--text-secondary);
    margin-bottom: 0.375rem;
    font-weight: 500;
}

.info-value {
    font-size: 1.125rem;
    font-weight: 600;
    color: var(--text-primary);
}

.info-features {
    padding-top: 1rem;
    border-top: 1px solid var(--border-color);
}

.feature-badges {
    display: flex;
    flex-wrap: wrap;
    gap: 0.75rem;
}

.feature-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 500;
}

.feature-badge.transferable {
    background: var(--info-color)15;
    color: var(--info-color);
}

.feature-badge.burnable {
    background: var(--warning-color)15;
    color: var(--warning-color);
}

.feature-badge.mintable {
    background: var(--success-color)15;
    color: var(--success-color);
}

.fee-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1rem 0;
    border-bottom: 1px solid var(--border-color);
}

.fee-label {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1rem;
    color: var(--text-secondary);
    font-weight: 500;
}

.fee-label i {
    color: var(--primary-color);
}

.fee-value {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
}

.fee-divider {
    height: 1px;
    background: var(--border-color);
    margin: 1rem 0;
}

.info-item-full {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 0;
}

.contract-address {
    font-family: 'Monaco', 'Consolas', monospace;
    font-size: 0.875rem;
    padding: 0.5rem 0.75rem;
    background: var(--light-bg);
    border-radius: 8px;
    color: var(--text-primary);
    font-weight: 600;
}

/* About Section */
.crypto-about-section {
    margin-bottom: 2rem;
}

.crypto-about-card {
    background: var(--card-bg);
    border-radius: 20px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
    border: 1px solid var(--border-color);
}

.crypto-about-header {
    padding: 1.5rem;
    background: linear-gradient(135deg, var(--primary-color)10, var(--secondary-color)10);
    border-bottom: 1px solid var(--border-color);
}

.crypto-about-header h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
}

.crypto-about-body {
    padding: 2rem;
}

.crypto-description {
    font-size: 1.0625rem;
    line-height: 1.75;
    color: var(--text-secondary);
    margin-bottom: 1.5rem;
}

.crypto-links {
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
}

.crypto-link {
    display: inline-flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.875rem 1.5rem;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    transition: all 0.3s ease;
    border: 2px solid var(--border-color);
}

.crypto-link.website {
    background: var(--info-color)10;
    color: var(--info-color);
    border-color: var(--info-color)30;
}

.crypto-link.website:hover {
    background: var(--info-color);
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.crypto-link.whitepaper {
    background: var(--text-secondary)10;
    color: var(--text-secondary);
    border-color: var(--text-secondary)30;
}

.crypto-link.whitepaper:hover {
    background: var(--text-secondary);
    color: white;
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

/* Transactions Section */
.crypto-transactions-section {
    margin-bottom: 2rem;
}

.crypto-transactions-card {
    background: var(--card-bg);
    border-radius: 20px;
    box-shadow: var(--shadow-md);
    overflow: hidden;
    border: 1px solid var(--border-color);
}

.crypto-transactions-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 1.5rem;
    background: linear-gradient(135deg, var(--primary-color)10, var(--secondary-color)10);
    border-bottom: 1px solid var(--border-color);
}

.crypto-transactions-header h3 {
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
}

.crypto-refresh-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.625rem 1.25rem;
    background: white;
    border: 2px solid var(--border-color);
    border-radius: 10px;
    color: var(--text-primary);
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
}

.crypto-refresh-btn:hover {
    background: var(--primary-color);
    color: white;
    border-color: var(--primary-color);
    transform: translateY(-1px);
}

.crypto-transactions-body {
    padding: 1.5rem;
}

.transactions-table-wrapper {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.transaction-row {
    display: grid;
    grid-template-columns: auto 1fr auto auto auto;
    gap: 1.5rem;
    align-items: center;
    padding: 1.25rem;
    background: var(--light-bg);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.transaction-row:hover {
    background: var(--primary-color)08;
    transform: translateX(4px);
}

.transaction-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.5rem 1rem;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 600;
}

.transaction-badge.buy {
    background: var(--success-color)15;
    color: var(--success-color);
}

.transaction-badge.sell {
    background: var(--danger-color)15;
    color: var(--danger-color);
}

.transaction-badge.create {
    background: var(--info-color)15;
    color: var(--info-color);
}

.transaction-amount {
    display: flex;
    align-items: baseline;
    gap: 0.5rem;
}

.amount-value {
    font-size: 1.125rem;
    font-weight: 700;
    color: var(--text-primary);
}

.amount-symbol {
    font-size: 0.875rem;
    color: var(--text-secondary);
    font-weight: 600;
}

.transaction-price,
.transaction-total {
    text-align: right;
}

.price-label,
.total-label {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-bottom: 0.25rem;
}

.price-value,
.total-value {
    font-size: 1rem;
    font-weight: 600;
    color: var(--text-primary);
}

.transaction-date {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--text-secondary);
}

.no-transactions {
    text-align: center;
    padding: 4rem 2rem;
}

.no-transactions-icon {
    width: 80px;
    height: 80px;
    margin: 0 auto 1.5rem;
    background: linear-gradient(135deg, var(--primary-color)15, var(--secondary-color)15);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 2rem;
    color: var(--primary-color);
}

.no-transactions h4 {
    font-size: 1.5rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.5rem;
}

.no-transactions p {
    font-size: 1rem;
    color: var(--text-secondary);
}

/* Animations */
@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.fa-sync-alt.spinning {
    animation: spin 1s linear infinite;
}

/* Responsive Design */
@media (max-width: 1024px) {
    .crypto-stats-grid {
        grid-template-columns: repeat(3, 1fr);
    }
}

@media (max-width: 768px) {
    .crypto-hero-content {
        padding: 1.5rem;
    }
    
    .crypto-name {
        font-size: 1.75rem;
    }
    
    .crypto-current-price {
        font-size: 1.75rem;
    }
    
    .crypto-action-buttons {
        width: 100%;
    }
    
    .crypto-btn {
        flex: 1;
        justify-content: center;
    }
    
    .crypto-stats-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
    }
    
    .crypto-wallet-grid {
        grid-template-columns: 1fr;
    }
    
    .transaction-row {
        grid-template-columns: 1fr;
        gap: 1rem;
    }
    
    .transaction-price,
    .transaction-total {
        text-align: left;
    }
}

@media (max-width: 480px) {
    .crypto-stats-grid {
        grid-template-columns: 1fr;
    }
    
    .crypto-price-section {
        flex-direction: column;
        align-items: flex-start;
    }
}
</style>

<script>
function toggleWatchlist() {
    const btn = event.target.closest('button');
    const icon = btn.querySelector('i');
    const isWatchlisted = btn.classList.contains('watchlisted');
    
    if (isWatchlisted) {
        btn.innerHTML = '<i class="fas fa-star me-2"></i>WATCHLIST';
        btn.classList.remove('watchlisted', 'btn-warning');
        btn.classList.add('btn-outline-secondary');
        showNotification('Removed from watchlist', 'info');
    } else {
        btn.innerHTML = '<i class="fas fa-star me-2"></i>WATCHING';
        btn.classList.add('watchlisted', 'btn-warning');
        btn.classList.remove('btn-outline-secondary');
        showNotification('Added to watchlist', 'success');
    }
    
    // Here you would typically make an AJAX call to update the backend
    // fetch('/api/watchlist/toggle', { method: 'POST', ... })
}

function refreshTransactions() {
    const refreshBtn = document.querySelector('[onclick="refreshTransactions()"] i');
    refreshBtn.classList.add('spinning');
    
    // Simulate refresh - replace with actual AJAX call
    fetch(window.location.href, {
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.text())
    .then(html => {
        // Update transactions table
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        const newTransactionsTable = doc.querySelector('#transactions-table');
        if (newTransactionsTable) {
            document.querySelector('#transactions-table').innerHTML = newTransactionsTable.innerHTML;
        }
        
        refreshBtn.classList.remove('spinning');
        showNotification('Transactions refreshed', 'success');
    })
    .catch(error => {
        refreshBtn.classList.remove('spinning');
        showNotification('Failed to refresh transactions', 'error');
    });
}

function showNotification(message, type) {
    // Simple notification system
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'error' ? 'danger' : type === 'success' ? 'success' : 'info'} notification-toast`;
    notification.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        animation: slideInRight 0.3s ease;
    `;
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-${type === 'error' ? 'exclamation-circle' : type === 'success' ? 'check-circle' : 'info-circle'} me-2"></i>
            ${message}
        </div>
    `;
    
    document.body.appendChild(notification);
    
    setTimeout(() => {
        notification.style.animation = 'slideOutRight 0.3s ease';
        setTimeout(() => {
            document.body.removeChild(notification);
        }, 300);
    }, 3000);
}

// Add notification animations
const style = document.createElement('style');
style.textContent = `
@keyframes slideInRight {
    from { transform: translateX(100%); opacity: 0; }
    to { transform: translateX(0); opacity: 1; }
}

@keyframes slideOutRight {
    from { transform: translateX(0); opacity: 1; }
    to { transform: translateX(100%); opacity: 0; }
}
`;
document.head.appendChild(style);

// Add some interactive features
document.addEventListener('DOMContentLoaded', function() {
    // Animate numbers on page load
    const numbers = document.querySelectorAll('.h4, .h5, .h6');
    numbers.forEach((number, index) => {
        if (number.textContent.includes('$') || number.textContent.includes('%')) {
            number.style.opacity = '0';
            setTimeout(() => {
                number.style.opacity = '1';
            }, index * 100);
        }
    });
    
    // Add click effect to cards (but not wallet cards)
    const cards = document.querySelectorAll('.card:not(.wallet-gradient)');
    cards.forEach(card => {
        card.addEventListener('click', function(e) {
            if (!e.target.closest('.btn') && !e.target.closest('a')) {
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = '';
                }, 150);
            }
        });
    });
    
    // Auto-refresh data every 30 seconds
    setInterval(() => {
        console.log('Auto-refreshing data...');
        // Uncomment the line below for actual auto-refresh
        // refreshTransactions();
    }, 30000);
});
</script>
@endsection