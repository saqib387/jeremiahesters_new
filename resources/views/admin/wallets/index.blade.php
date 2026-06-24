@extends('voyager::master')

@section('page_title', 'Wallet Management')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <h1 class="page-title">
                    <i class="voyager-wallet"></i> Wallet Management
                </h1>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('voyager.wallets.export', request()->query()) }}" class="btn btn-success">
                    <i class="voyager-download"></i> <span>Export CSV</span>
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-2">
                <div class="panel widget center bgimage" style="background-color:#3498db;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>{{ number_format($stats['total_wallets']) }}</h4>
                        <p>Total Wallets</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel widget center bgimage" style="background-color:#2ecc71;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>{{ number_format($stats['active_wallets']) }}</h4>
                        <p>Active</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel widget center bgimage" style="background-color:#f39c12;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>{{ number_format($stats['wallets_with_balance']) }}</h4>
                        <p>With Balance</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel widget center bgimage" style="background-color:#9b59b6;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>${{ number_format($stats['total_balance_usd'], 2) }}</h4>
                        <p>Total Balance USD</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel widget center bgimage" style="background-color:#e74c3c;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>{{ number_format($stats['unique_users']) }}</h4>
                        <p>Unique Users</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <form method="GET" action="{{ route('voyager.wallets.index') }}" class="form-inline">
                            <div class="form-group">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="Search wallets, users, cryptocurrencies..." 
                                       value="{{ request('search') }}">
                            </div>
                            
                            <div class="form-group">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                    <option value="with_balance" {{ request('status') == 'with_balance' ? 'selected' : '' }}>With Balance</option>
                                    <option value="empty" {{ request('status') == 'empty' ? 'selected' : '' }}>Empty</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <select name="cryptocurrency_id" class="form-control">
                                    <option value="">All Cryptocurrencies</option>
                                    @foreach($cryptocurrencies as $crypto)
                                        <option value="{{ $crypto->id }}" {{ request('cryptocurrency_id') == $crypto->id ? 'selected' : '' }}>
                                            {{ $crypto->name }} ({{ $crypto->symbol }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <select name="sort_by" class="form-control">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Created</option>
                                    <option value="updated_at" {{ request('sort_by') == 'updated_at' ? 'selected' : '' }}>Sort by Updated</option>
                                    <option value="balance" {{ request('sort_by') == 'balance' ? 'selected' : '' }}>Sort by Balance</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <select name="sort_dir" class="form-control">
                                    <option value="desc" {{ request('sort_dir') == 'desc' ? 'selected' : '' }}>Descending</option>
                                    <option value="asc" {{ request('sort_dir') == 'asc' ? 'selected' : '' }}>Ascending</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="voyager-search"></i> Filter
                            </button>
                            
                            <a href="{{ route('voyager.wallets.index') }}" class="btn btn-default">
                                <i class="voyager-refresh"></i> Clear
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Wallets Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>User</th>
                                        <th>Cryptocurrency</th>
                                        <th>Balance</th>
                                        <th>Balance USD</th>
                                        <th>Wallet Address</th>
                                        <th>Status</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($wallets as $wallet)
                                        <tr>
                                            <td>
                                                <strong>#{{ $wallet->id }}</strong>
                                            </td>
                                            <td>
                                                @if($wallet->user)
                                                    <div>
                                                        <strong>{{ $wallet->user->name }}</strong><br>
                                                        <small class="text-muted">{{ $wallet->user->email }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Unknown User</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($wallet->cryptocurrency)
                                                    <div class="media">
                                                        @if($wallet->cryptocurrency->logo)
                                                            <div class="media-left">
                                                                <img src="{{ Storage::url($wallet->cryptocurrency->logo) }}" 
                                                                     alt="{{ $wallet->cryptocurrency->name }}" 
                                                                     class="media-object" 
                                                                     style="width: 30px; height: 30px; border-radius: 50%;">
                                                            </div>
                                                        @endif
                                                        <div class="media-body">
                                                            <strong>{{ $wallet->cryptocurrency->name }}</strong><br>
                                                            <small class="text-muted">{{ $wallet->cryptocurrency->symbol }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Unknown Crypto</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $wallet->formatted_balance }}</strong>
                                                @if($wallet->cryptocurrency)
                                                    <br><small class="text-muted">{{ $wallet->cryptocurrency->symbol }}</small>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $wallet->formatted_balance_usd }}</strong>
                                            </td>
                                            <td>
                                                @if($wallet->wallet_address)
                                                    <code title="{{ $wallet->wallet_address }}">{{ $wallet->masked_address }}</code>
                                                    @if($wallet->has_private_key)
                                                        <i class="voyager-key text-warning" title="Has Private Key"></i>
                                                    @endif
                                                @else
                                                    <span class="text-muted">No Address</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="label {{ $wallet->status_badge_class }}">
                                                    {{ $wallet->status_text }}
                                                </span>
                                            </td>
                                            <td>
                                                {{ $wallet->created_at ? $wallet->created_at->format('M d, Y') : 'N/A' }}
                                            </td>
                                            <td>
                                                <div class="btn-group-horizontal">
                                                    <a href="#" 
                                                       onclick="showWalletDetails({{ $wallet->id }}); return false;" 
                                                       data-toggle="modal" 
                                                       data-target="#walletModal"
                                                       class="btn btn-sm btn-info"
                                                       title="View Details">
                                                        <i class="voyager-eye"></i> Details
                                                    </a>
                                                    
                                                    <a href="{{ route('voyager.wallets.toggle-status', $wallet->id) }}" 
                                                       onclick="return confirm('Are you sure you want to {{ $wallet->is_active ? 'deactivate' : 'activate' }} this wallet?')"
                                                       class="btn btn-sm {{ $wallet->is_active ? 'btn-warning' : 'btn-success' }}"
                                                       title="{{ $wallet->is_active ? 'Deactivate' : 'Activate' }} Wallet">
                                                        <i class="voyager-power"></i> 
                                                        {{ $wallet->is_active ? 'Deactivate' : 'Activate' }}
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9" class="text-center">
                                                <p>No wallets found.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($wallets->hasPages())
                            <div class="text-center">
                                {{ $wallets->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Wallet Details Modal -->
    <div class="modal fade" id="walletModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                    <h4 class="modal-title">Wallet Details</h4>
                </div>
                <div class="modal-body" id="walletModalBody">
                    <div class="text-center">
                        <i class="voyager-refresh" style="font-size: 24px;"></i>
                        <p>Loading wallet details...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
<script>
function showWalletDetails(walletId) {
    // Reset modal content
    $('#walletModalBody').html(`
        <div class="text-center">
            <i class="voyager-refresh" style="font-size: 24px;"></i>
            <p>Loading wallet details...</p>
        </div>
    `);
    
    // Fetch wallet details
    $.get('{{ route("voyager.wallets.details", ":id") }}'.replace(':id', walletId))
        .done(function(data) {
            const html = `
                <div class="row">
                    <div class="col-md-6">
                        <h5>User Information</h5>
                        <table class="table table-bordered">
                            <tr><td><strong>Name:</strong></td><td>${data.user_name}</td></tr>
                            <tr><td><strong>Email:</strong></td><td>${data.user_email}</td></tr>
                        </table>
                        
                        <h5>Cryptocurrency</h5>
                        <table class="table table-bordered">
                            <tr><td><strong>Name:</strong></td><td>${data.cryptocurrency_name}</td></tr>
                            <tr><td><strong>Symbol:</strong></td><td>${data.cryptocurrency_symbol}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Wallet Information</h5>
                        <table class="table table-bordered">
                            <tr><td><strong>Balance:</strong></td><td>${data.balance} ${data.cryptocurrency_symbol}</td></tr>
                            <tr><td><strong>Balance USD:</strong></td><td>${data.balance_usd}</td></tr>
                            <tr><td><strong>Status:</strong></td><td><span class="label ${data.is_active ? 'label-success' : 'label-danger'}">${data.status_text}</span></td></tr>
                            <tr><td><strong>Has Private Key:</strong></td><td>${data.has_private_key ? '<i class="voyager-check text-success"></i> Yes' : '<i class="voyager-x text-danger"></i> No'}</td></tr>
                        </table>
                        
                        <h5>Timestamps</h5>
                        <table class="table table-bordered">
                            <tr><td><strong>Created:</strong></td><td>${data.created_at}</td></tr>
                            <tr><td><strong>Updated:</strong></td><td>${data.updated_at}</td></tr>
                        </table>
                    </div>
                </div>
                
                ${data.wallet_address ? `
                <div class="row">
                    <div class="col-md-12">
                        <h5>Wallet Address</h5>
                        <div class="well">
                            <code style="font-size: 12px; word-break: break-all;">${data.wallet_address}</code>
                        </div>
                    </div>
                </div>
                ` : ''}
            `;
            
            $('#walletModalBody').html(html);
        })
        .fail(function() {
            $('#walletModalBody').html(`
                <div class="alert alert-danger">
                    <strong>Error!</strong> Failed to load wallet details.
                </div>
            `);
        });
}
</script>

<style>
/* Simple button styling */
.btn-group-horizontal {
    display: inline-block;
}

.btn-group-horizontal .btn {
    margin-right: 5px;
}

.btn-group-horizontal .btn:last-child {
    margin-right: 0;
}

/* Ensure buttons are properly spaced */
.btn-sm {
    padding: 5px 10px;
    font-size: 12px;
    line-height: 1.5;
    border-radius: 3px;
}
</style>
@stop