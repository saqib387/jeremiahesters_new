@extends('layouts.generic')

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/wallet.css') }}">
@endsection

@section('content')
<div class="wallet-container">
    <div class="row mb-3 wallet-header">
        <div class="col-12">
            <h1>My Wallet</h1>
            <p class="lead">Manage your cryptocurrency tokens and transactions</p>
        </div>
    </div>
    
    <!-- Wallet Balance -->
    <div class="row mb-4">
        <div class="col-12 col-md-6 mb-3 mb-md-0">
            <div class="wallet-card">
                <div class="wallet-card-header bg-primary">
                    <h5>Total Balance</h5>
                </div>
                <div class="wallet-card-body">
                    <div class="balance-display">
                        <h2>${{ number_format($totalBalance, 2) }}</h2>
                    </div>
                    <div class="balance-item">
                        <span>Available for trading</span>
                        <span>${{ number_format($availableBalance, 2) }}</span>
                    </div>
                    <div class="balance-item">
                        <span>Pending transactions</span>
                        <span>${{ number_format($pendingBalance, 2) }}</span>
                    </div>
                    <div class="wallet-actions">
                        <a href="{{ route('cryptocurrency.deposit') }}" class="wallet-btn wallet-btn-success">
                            <i class="fas fa-arrow-down"></i> Deposit
                        </a>
                        <a href="{{ route('cryptocurrency.withdraw') }}" class="wallet-btn wallet-btn-outline">
                            <i class="fas fa-arrow-up"></i> Withdraw
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6">
            <div class="wallet-card">
                <div class="wallet-card-header bg-info">
                    <h5>Quick Actions</h5>
                </div>
                <div class="wallet-card-body">
                    <div class="quick-actions-grid">
                        <a href="{{ route('cryptocurrency.marketplace') }}" class="action-button">
                            <i class="fas fa-store"></i>
                            <span>Marketplace</span>
                        </a>
                        <a href="{{ route('cryptocurrency.create') }}" class="action-button">
                            <i class="fas fa-plus-circle"></i>
                            <span>Create Token</span>
                        </a>
                        <a href="#transaction-history" class="action-button">
                            <i class="fas fa-history"></i>
                            <span>History</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- My Tokens -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="wallet-card">
                <div class="wallet-card-header">
                    <h5>My Tokens</h5>
                </div>
                <div class="wallet-card-body p-0">
                    @forelse($wallets as $wallet)
                        <!-- Desktop Table View -->
                        <div class="tokens-table-wrapper d-none d-md-block">
                            <table class="tokens-table">
                                <thead>
                                    <tr>
                                        <th>Token</th>
                                        <th>Balance</th>
                                        <th>Value</th>
                                        <th>Price</th>
                                        <th>24h Change</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($wallets as $wallet)
                                    <tr>
                                        <td>
                                            <div class="token-cell">
                                                @if($wallet->cryptocurrency->logo)
                                                    <img src="{{ asset('storage/' . $wallet->cryptocurrency->logo) }}" alt="{{ $wallet->cryptocurrency->name }}" class="token-logo">
                                                @else
                                                    <div class="token-symbol-circle">
                                                        {{ substr($wallet->cryptocurrency->symbol, 0, 1) }}
                                                    </div>
                                                @endif
                                                <div class="token-info">
                                                    <div class="token-name">{{ $wallet->cryptocurrency->name }}</div>
                                                    <div class="token-symbol">{{ $wallet->cryptocurrency->symbol }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td><strong>{{ number_format($wallet->balance) }}</strong></td>
                                        <td><strong>${{ number_format($wallet->balance * $wallet->cryptocurrency->current_price, 2) }}</strong></td>
                                        <td>${{ number_format($wallet->cryptocurrency->current_price, 8) }}</td>
                                        <td>
                                            @if($wallet->cryptocurrency->price_change_24h > 0)
                                                <span class="price-change positive">
                                                    <i class="fas fa-arrow-up"></i>
                                                    +{{ number_format($wallet->cryptocurrency->price_change_24h, 2) }}%
                                                </span>
                                            @elseif($wallet->cryptocurrency->price_change_24h < 0)
                                                <span class="price-change negative">
                                                    <i class="fas fa-arrow-down"></i>
                                                    {{ number_format($wallet->cryptocurrency->price_change_24h, 2) }}%
                                                </span>
                                            @else
                                                <span class="price-change neutral">0.00%</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="token-actions">
                                                <a href="{{ route('cryptocurrency.show', $wallet->cryptocurrency->id) }}" class="btn btn-sm btn-outline-primary">Details</a>
                                                <a href="{{ route('cryptocurrency.buy.form', $wallet->cryptocurrency->id) }}" class="btn btn-sm btn-primary">Buy</a>
                                                <a href="{{ route('cryptocurrency.sell', $wallet->cryptocurrency->id) }}" class="btn btn-sm btn-outline-secondary">Sell</a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="tokens-mobile-list d-md-none px-3 py-2">
                            @foreach($wallets as $wallet)
                            <div class="token-mobile-card">
                                <div class="token-mobile-header">
                                    <div class="token-cell">
                                        @if($wallet->cryptocurrency->logo)
                                            <img src="{{ asset('storage/' . $wallet->cryptocurrency->logo) }}" alt="{{ $wallet->cryptocurrency->name }}" class="token-logo">
                                        @else
                                            <div class="token-symbol-circle">
                                                {{ substr($wallet->cryptocurrency->symbol, 0, 1) }}
                                            </div>
                                        @endif
                                        <div class="token-info">
                                            <div class="token-name">{{ $wallet->cryptocurrency->name }}</div>
                                            <div class="token-symbol">{{ $wallet->cryptocurrency->symbol }}</div>
                                        </div>
                                    </div>
                                    @if($wallet->cryptocurrency->price_change_24h > 0)
                                        <span class="price-change positive">
                                            <i class="fas fa-arrow-up"></i>
                                            +{{ number_format($wallet->cryptocurrency->price_change_24h, 2) }}%
                                        </span>
                                    @elseif($wallet->cryptocurrency->price_change_24h < 0)
                                        <span class="price-change negative">
                                            <i class="fas fa-arrow-down"></i>
                                            {{ number_format($wallet->cryptocurrency->price_change_24h, 2) }}%
                                        </span>
                                    @else
                                        <span class="price-change neutral">0.00%</span>
                                    @endif
                                </div>
                                <div class="token-mobile-info">
                                    <div class="token-mobile-info-item">
                                        <div class="token-mobile-info-label">Balance</div>
                                        <div class="token-mobile-info-value">{{ number_format($wallet->balance) }}</div>
                                    </div>
                                    <div class="token-mobile-info-item">
                                        <div class="token-mobile-info-label">Value</div>
                                        <div class="token-mobile-info-value">${{ number_format($wallet->balance * $wallet->cryptocurrency->current_price, 2) }}</div>
                                    </div>
                                    <div class="token-mobile-info-item">
                                        <div class="token-mobile-info-label">Price</div>
                                        <div class="token-mobile-info-value">${{ number_format($wallet->cryptocurrency->current_price, 8) }}</div>
                                    </div>
                                </div>
                                <div class="token-mobile-actions">
                                    <a href="{{ route('cryptocurrency.show', $wallet->cryptocurrency->id) }}" class="btn btn-sm btn-outline-primary">Details</a>
                                    <a href="{{ route('cryptocurrency.buy.form', $wallet->cryptocurrency->id) }}" class="btn btn-sm btn-primary">Buy</a>
                                    <a href="{{ route('cryptocurrency.sell', $wallet->cryptocurrency->id) }}" class="btn btn-sm btn-outline-secondary">Sell</a>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-wallet"></i>
                            </div>
                            <h4>You don't own any tokens yet</h4>
                            <p>Start by buying tokens from content creators you support</p>
                            <a href="{{ route('cryptocurrency.marketplace') }}" class="btn btn-primary">Browse Marketplace</a>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    
    <!-- Transaction History -->
    <div class="row" id="transaction-history">
        <div class="col-12">
            <div class="wallet-card">
                <div class="wallet-card-header" style="flex-wrap: wrap; gap: 12px;">
                    <h5>Transaction History</h5>
                    <div class="filter-buttons d-none d-md-flex">
                        <a href="{{ route('cryptocurrency.transactions', ['type' => 'all']) }}" class="filter-btn {{ request('type', 'all') == 'all' ? 'active' : '' }}">All</a>
                        <a href="{{ route('cryptocurrency.transactions', ['type' => 'buy']) }}" class="filter-btn {{ request('type') == 'buy' ? 'active' : '' }}">Buy</a>
                        <a href="{{ route('cryptocurrency.transactions', ['type' => 'sell']) }}" class="filter-btn {{ request('type') == 'sell' ? 'active' : '' }}">Sell</a>
                        <a href="{{ route('cryptocurrency.transactions', ['type' => 'transfer']) }}" class="filter-btn {{ request('type') == 'transfer' ? 'active' : '' }}">Transfer</a>
                    </div>
                </div>
                <div class="filter-buttons d-md-none px-3 py-2" style="background: var(--wallet-bg-secondary); border-bottom: 1px solid var(--wallet-border);">
                    <a href="{{ route('cryptocurrency.transactions', ['type' => 'all']) }}" class="filter-btn {{ request('type', 'all') == 'all' ? 'active' : '' }}">All</a>
                    <a href="{{ route('cryptocurrency.transactions', ['type' => 'buy']) }}" class="filter-btn {{ request('type') == 'buy' ? 'active' : '' }}">Buy</a>
                    <a href="{{ route('cryptocurrency.transactions', ['type' => 'sell']) }}" class="filter-btn {{ request('type') == 'sell' ? 'active' : '' }}">Sell</a>
                    <a href="{{ route('cryptocurrency.transactions', ['type' => 'transfer']) }}" class="filter-btn {{ request('type') == 'transfer' ? 'active' : '' }}">Transfer</a>
                </div>
                <div class="wallet-card-body p-0">
                    @forelse($transactions as $transaction)
                        <!-- Desktop Table View -->
                        <div class="tokens-table-wrapper d-none d-md-block">
                            <table class="tokens-table">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Type</th>
                                        <th>Token</th>
                                        <th>Amount</th>
                                        <th>Price</th>
                                        <th>Total</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($transactions as $txn)
                                    <tr>
                                        <td>{{ $txn->created_at->format('M d, Y H:i') }}</td>
                                        <td>
                                            @if($txn->type == 'buy')
                                                <span class="transaction-badge buy">Buy</span>
                                            @elseif($txn->type == 'sell')
                                                <span class="transaction-badge sell">Sell</span>
                                            @elseif($txn->type == 'transfer')
                                                <span class="transaction-badge transfer">Transfer</span>
                                            @else
                                                <span class="transaction-badge">{{ ucfirst($txn->type) }}</span>
                                            @endif
                                        </td>
                                        <td><strong>{{ $txn->cryptocurrency->symbol }}</strong></td>
                                        <td>{{ number_format($txn->amount) }}</td>
                                        <td>${{ number_format($txn->price_per_token, 8) }}</td>
                                        <td><strong>${{ number_format($txn->total_amount, 2) }}</strong></td>
                                        <td>
                                            @if($txn->status == 'completed')
                                                <span class="status-badge completed">Completed</span>
                                            @elseif($txn->status == 'pending')
                                                <span class="status-badge pending">Pending</span>
                                            @elseif($txn->status == 'failed')
                                                <span class="status-badge failed">Failed</span>
                                            @else
                                                <span class="status-badge">{{ ucfirst($txn->status) }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Mobile Card View -->
                        <div class="d-md-none px-3 py-2">
                            @foreach($transactions as $txn)
                            <div class="transaction-mobile-card">
                                <div class="transaction-mobile-header">
                                    <div>
                                        @if($txn->type == 'buy')
                                            <span class="transaction-badge buy">Buy</span>
                                        @elseif($txn->type == 'sell')
                                            <span class="transaction-badge sell">Sell</span>
                                        @elseif($txn->type == 'transfer')
                                            <span class="transaction-badge transfer">Transfer</span>
                                        @else
                                            <span class="transaction-badge">{{ ucfirst($txn->type) }}</span>
                                        @endif
                                    </div>
                                    <div>
                                        @if($txn->status == 'completed')
                                            <span class="status-badge completed">Completed</span>
                                        @elseif($txn->status == 'pending')
                                            <span class="status-badge pending">Pending</span>
                                        @elseif($txn->status == 'failed')
                                            <span class="status-badge failed">Failed</span>
                                        @else
                                            <span class="status-badge">{{ ucfirst($txn->status) }}</span>
                                        @endif
                                    </div>
                                </div>
                                <dl class="transaction-mobile-details">
                                    <dt>Date</dt>
                                    <dd>{{ $txn->created_at->format('M d, Y') }}<br><small>{{ $txn->created_at->format('H:i') }}</small></dd>
                                    
                                    <dt>Token</dt>
                                    <dd><strong>{{ $txn->cryptocurrency->symbol }}</strong></dd>
                                    
                                    <dt>Amount</dt>
                                    <dd>{{ number_format($txn->amount) }}</dd>
                                    
                                    <dt>Price</dt>
                                    <dd>${{ number_format($txn->price_per_token, 6) }}</dd>
                                    
                                    <dt>Total</dt>
                                    <dd><strong>${{ number_format($txn->total_amount, 2) }}</strong></dd>
                                </dl>
                            </div>
                            @endforeach
                        </div>
                    @empty
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="fas fa-history"></i>
                            </div>
                            <h4>No transactions found</h4>
                            <p>Your transaction history will appear here</p>
                        </div>
                    @endforelse
                </div>
                @if(isset($transactions) && $transactions->hasPages())
                <div class="wallet-card-body border-top">
                    <div class="d-flex justify-content-center">
                        {{ $transactions->links() }}
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

@endsection 