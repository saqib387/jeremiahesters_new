@extends('layouts.generic')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-4">Transaction History</h1>
                    <p class="lead">View all your cryptocurrency transactions</p>
                </div>
                <div>
                    <a href="{{ route('cryptocurrency.wallet') }}" class="btn btn-outline-primary">
                        <i class="fas fa-wallet mr-1"></i> Back to Wallet
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Filters</h5>
                    <div class="btn-group">
                        <a href="{{ route('cryptocurrency.transactions', ['type' => 'all']) }}" class="btn btn-sm btn-{{ $type == 'all' ? 'primary' : 'outline-secondary' }}">All</a>
                        <a href="{{ route('cryptocurrency.transactions', ['type' => 'buy']) }}" class="btn btn-sm btn-{{ $type == 'buy' ? 'primary' : 'outline-secondary' }}">Buy</a>
                        <a href="{{ route('cryptocurrency.transactions', ['type' => 'sell']) }}" class="btn btn-sm btn-{{ $type == 'sell' ? 'primary' : 'outline-secondary' }}">Sell</a>
                        <a href="{{ route('cryptocurrency.transactions', ['type' => 'transfer']) }}" class="btn btn-sm btn-{{ $type == 'transfer' ? 'primary' : 'outline-secondary' }}">Transfer</a>
                        <a href="{{ route('cryptocurrency.transactions', ['type' => 'deposit']) }}" class="btn btn-sm btn-{{ $type == 'deposit' ? 'primary' : 'outline-secondary' }}">Deposit</a>
                        <a href="{{ route('cryptocurrency.transactions', ['type' => 'withdraw']) }}" class="btn btn-sm btn-{{ $type == 'withdraw' ? 'primary' : 'outline-secondary' }}">Withdraw</a>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
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
                                @forelse($transactions as $transaction)
                                <tr>
                                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        @if($transaction->type == 'buy')
                                            <span class="badge bg-success">Buy</span>
                                        @elseif($transaction->type == 'sell')
                                            <span class="badge bg-danger">Sell</span>
                                        @elseif($transaction->type == 'transfer')
                                            <span class="badge bg-info">Transfer</span>
                                        @elseif($transaction->type == 'deposit')
                                            <span class="badge bg-primary">Deposit</span>
                                        @elseif($transaction->type == 'withdraw')
                                            <span class="badge bg-warning">Withdraw</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($transaction->type) }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($transaction->cryptocurrency))
                                            {{ $transaction->cryptocurrency->symbol }}
                                        @else
                                            USD
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($transaction->cryptocurrency))
                                            {{ number_format($transaction->amount) }}
                                        @else
                                            ${{ number_format($transaction->amount, 2) }}
                                        @endif
                                    </td>
                                    <td>
                                        @if(isset($transaction->price_per_token) && $transaction->price_per_token > 0)
                                            ${{ number_format($transaction->price_per_token, 8) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>${{ number_format($transaction->total_price, 2) }}</td>
                                    <td>
                                        @if($transaction->status == 'completed')
                                            <span class="badge bg-success">Completed</span>
                                        @elseif($transaction->status == 'pending')
                                            <span class="badge bg-warning">Pending</span>
                                        @elseif($transaction->status == 'failed')
                                            <span class="badge bg-danger">Failed</span>
                                        @else
                                            <span class="badge bg-secondary">{{ ucfirst($transaction->status) }}</span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4">
                                        <p class="mb-0">No transactions found.</p>
                                        @if($type != 'all')
                                            <a href="{{ route('cryptocurrency.transactions') }}" class="btn btn-sm btn-outline-primary mt-2">View All Transactions</a>
                                        @endif
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white">
                    <div class="d-flex justify-content-center">
                        {{ $transactions->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Transaction Statistics</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6 mb-4">
                            <h6 class="text-muted mb-1">Total Transactions</h6>
                            <h3>{{ $transactions->total() }}</h3>
                        </div>
                        <div class="col-6 mb-4">
                            <h6 class="text-muted mb-1">This Month</h6>
                            <h3>{{ $transactions->where('created_at', '>=', now()->startOfMonth())->count() }}</h3>
                        </div>
                        <div class="col-6">
                            <h6 class="text-muted mb-1">Successful</h6>
                            <h3>{{ $transactions->where('status', 'completed')->count() }}</h3>
                        </div>
                        <div class="col-6">
                            <h6 class="text-muted mb-1">Pending</h6>
                            <h3>{{ $transactions->where('status', 'pending')->count() }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Need Help?</h5>
                </div>
                <div class="card-body">
                    <p>If you have any questions about your transactions:</p>
                    <ul class="mb-4">
                        <li>For pending transactions, please allow up to 24 hours for processing</li>
                        <li>For failed transactions, check your payment details and try again</li>
                        <li>For withdrawal issues, contact our support team</li>
                    </ul>
                    <div class="d-grid">
                        <a href="#" class="btn btn-outline-primary">Contact Support</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .table th {
        font-weight: 500;
        color: #6c757d;
        border-top: none;
    }
    
    .badge {
        padding: 0.5em 0.8em;
        font-weight: 500;
    }
    
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 15px rgba(0,0,0,.05) !important;
    }
    
    .btn-group .btn {
        min-width: 80px;
    }
</style>
@endpush
@endsection 