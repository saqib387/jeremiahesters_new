@extends('voyager::master')

@section('page_title', 'Revenue Management')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <h1 class="page-title">
                    <i class="voyager-dollar"></i> Revenue Management
                </h1>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('voyager.revenue.export', request()->query()) }}" class="btn btn-success">
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
                        <h4>{{ number_format($stats['total_revenue_shares']) }}</h4>
                        <p>Total Shares</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel widget center bgimage" style="background-color:#2ecc71;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>{{ number_format($stats['distributed_shares']) }}</h4>
                        <p>Distributed</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel widget center bgimage" style="background-color:#f39c12;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>{{ number_format($stats['pending_shares']) }}</h4>
                        <p>Pending</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel widget center bgimage" style="background-color:#e74c3c;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>{{ number_format($stats['overdue_shares']) }}</h4>
                        <p>Overdue</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel widget center bgimage" style="background-color:#9b59b6;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>${{ number_format($stats['total_distributed_amount'], 2) }}</h4>
                        <p>Distributed Amount</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel widget center bgimage" style="background-color:#1abc9c;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>${{ number_format($stats['pending_amount'], 2) }}</h4>
                        <p>Pending Amount</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <form method="GET" action="{{ route('voyager.revenue.index') }}" class="form-inline">
                            <div class="form-group">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="Search users, cryptocurrencies, transactions..." 
                                       value="{{ request('search') }}">
                            </div>
                            
                            <div class="form-group">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="distributed" {{ request('status') == 'distributed' ? 'selected' : '' }}>Distributed</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                                    <option value="overdue" {{ request('status') == 'overdue' ? 'selected' : '' }}>Overdue</option>
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
                                <select name="amount_range" class="form-control">
                                    <option value="">All Amounts</option>
                                    <option value="small" {{ request('amount_range') == 'small' ? 'selected' : '' }}>Small (< $100)</option>
                                    <option value="medium" {{ request('amount_range') == 'medium' ? 'selected' : '' }}>Medium ($100 - $1,000)</option>
                                    <option value="large" {{ request('amount_range') == 'large' ? 'selected' : '' }}>Large (> $1,000)</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <select name="sort_by" class="form-control">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Sort by Created</option>
                                    <option value="revenue_amount" {{ request('sort_by') == 'revenue_amount' ? 'selected' : '' }}>Sort by Revenue</option>
                                    <option value="distribution_amount" {{ request('sort_by') == 'distribution_amount' ? 'selected' : '' }}>Sort by Distribution</option>
                                    <option value="percentage" {{ request('sort_by') == 'percentage' ? 'selected' : '' }}>Sort by Percentage</option>
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
                            
                            <a href="{{ route('voyager.revenue.index') }}" class="btn btn-default">
                                <i class="voyager-refresh"></i> Clear
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Revenue Shares Table -->
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
                                        <th>Transaction ID</th>
                                        <th>Percentage</th>
                                        <th>Revenue Amount</th>
                                        <th>Distribution Amount</th>
                                        <th>Status</th>
                                        <th>Priority</th>
                                        <th>Created</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($revenues as $revenue)
                                        <tr class="{{ $revenue->is_overdue ? 'warning' : '' }}">
                                            <td>
                                                <strong>#{{ $revenue->id }}</strong>
                                            </td>
                                            <td>
                                                @if($revenue->user)
                                                    <div>
                                                        <strong>{{ $revenue->user->name }}</strong><br>
                                                        <small class="text-muted">{{ $revenue->user->email }}</small>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Unknown User</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($revenue->cryptocurrency)
                                                    <div class="media">
                                                        @if($revenue->cryptocurrency->logo)
                                                            <div class="media-left">
                                                                <img src="{{ Storage::url($revenue->cryptocurrency->logo) }}" 
                                                                     alt="{{ $revenue->cryptocurrency->name }}" 
                                                                     class="media-object" 
                                                                     style="width: 30px; height: 30px; border-radius: 50%;">
                                                            </div>
                                                        @endif
                                                        <div class="media-body">
                                                            <strong>{{ $revenue->cryptocurrency->name }}</strong><br>
                                                            <small class="text-muted">{{ $revenue->cryptocurrency->symbol }}</small>
                                                        </div>
                                                    </div>
                                                @else
                                                    <span class="text-muted">Unknown Crypto</span>
                                                @endif
                                            </td>
                                            <td>
                                                @if($revenue->transaction_id)
                                                    <code>{{ substr($revenue->transaction_id, 0, 10) }}...</code>
                                                @else
                                                    <span class="text-muted">N/A</span>
                                                @endif
                                            </td>
                                            <td>
                                                <strong>{{ $revenue->formatted_percentage }}</strong>
                                            </td>
                                            <td>
                                                <strong>{{ $revenue->formatted_revenue_amount }}</strong>
                                                @if($revenue->cryptocurrency)
                                                    <small class="text-muted">{{ $revenue->cryptocurrency->symbol }}</small>
                                                @endif
                                                <br>
                                                <small class="text-success">{{ $revenue->formatted_revenue_amount_usd }}</small>
                                            </td>
                                            <td>
                                                <strong>{{ $revenue->formatted_distribution_amount }}</strong>
                                                @if($revenue->cryptocurrency)
                                                    <small class="text-muted">{{ $revenue->cryptocurrency->symbol }}</small>
                                                @endif
                                                <br>
                                                <small class="text-success">{{ $revenue->formatted_distribution_amount_usd }}</small>
                                            </td>
                                            <td>
                                                <span class="label {{ $revenue->status_badge_class }}">
                                                    {{ $revenue->status_text }}
                                                </span>
                                                @if($revenue->is_overdue)
                                                    <br><small class="text-danger"><i class="voyager-exclamation"></i> Overdue</small>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="{{ $revenue->priority_color }}">
                                                    <strong>{{ ucfirst($revenue->priority_level) }}</strong>
                                                </span>
                                            </td>
                                            <td>
                                                {{ $revenue->created_at ? $revenue->created_at->format('M d, Y') : 'N/A' }}
                                                <br>
                                                <small class="text-muted">{{ $revenue->time_since_created }}</small>
                                            </td>
                                            <td>
                                                <div class="btn-group-horizontal">
                                                    <a href="#" 
                                                       onclick="showRevenueDetails({{ $revenue->id }}); return false;" 
                                                       data-toggle="modal" 
                                                       data-target="#revenueModal"
                                                       class="btn btn-sm btn-info"
                                                       title="View Details">
                                                        <i class="voyager-eye"></i> Details
                                                    </a>
                                                    
                                                    @if($revenue->is_distributed)
                                                        <a href="{{ route('voyager.revenue.mark-pending', $revenue->id) }}" 
                                                           onclick="return confirm('Are you sure you want to mark this as pending?')"
                                                           class="btn btn-sm btn-warning"
                                                           title="Mark as Pending">
                                                            <i class="voyager-refresh"></i> Pending
                                                        </a>
                                                    @else
                                                        <a href="{{ route('voyager.revenue.mark-distributed', $revenue->id) }}" 
                                                           onclick="return confirm('Are you sure you want to mark this as distributed?')"
                                                           class="btn btn-sm btn-success"
                                                           title="Mark as Distributed">
                                                            <i class="voyager-check"></i> Distribute
                                                        </a>
                                                    @endif
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="11" class="text-center">
                                                <p>No revenue shares found.</p>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($revenues->hasPages())
                            <div class="text-center">
                                {{ $revenues->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Revenue Details Modal -->
    <div class="modal fade" id="revenueModal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                    <h4 class="modal-title">Revenue Share Details</h4>
                </div>
                <div class="modal-body" id="revenueModalBody">
                    <div class="text-center">
                        <i class="voyager-refresh" style="font-size: 24px;"></i>
                        <p>Loading revenue details...</p>
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
function showRevenueDetails(revenueId) {
    // Reset modal content
    $('#revenueModalBody').html(`
        <div class="text-center">
            <i class="voyager-refresh" style="font-size: 24px;"></i>
            <p>Loading revenue details...</p>
        </div>
    `);
    
    // Fetch revenue details
    $.get('{{ route("voyager.revenue.details", ":id") }}'.replace(':id', revenueId))
        .done(function(data) {
            let priorityBadge = '';
            switch(data.priority_level) {
                case 'high':
                    priorityBadge = '<span class="label label-danger">High Priority</span>';
                    break;
                case 'medium':
                    priorityBadge = '<span class="label label-warning">Medium Priority</span>';
                    break;
                case 'low':
                    priorityBadge = '<span class="label label-info">Low Priority</span>';
                    break;
                case 'completed':
                    priorityBadge = '<span class="label label-success">Completed</span>';
                    break;
            }
            
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
                        
                        <h5>Transaction</h5>
                        <table class="table table-bordered">
                            <tr><td><strong>Transaction ID:</strong></td><td>${data.transaction_id}</td></tr>
                            <tr><td><strong>Percentage:</strong></td><td>${data.percentage}</td></tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Revenue Information</h5>
                        <table class="table table-bordered">
                            <tr><td><strong>Revenue Amount:</strong></td><td>${data.revenue_amount} ${data.cryptocurrency_symbol}<br><small class="text-success">${data.revenue_amount_usd}</small></td></tr>
                            <tr><td><strong>Distribution Amount:</strong></td><td>${data.distribution_amount} ${data.cryptocurrency_symbol}<br><small class="text-success">${data.distribution_amount_usd}</small></td></tr>
                            <tr><td><strong>Status:</strong></td><td><span class="label ${data.is_distributed ? 'label-success' : 'label-warning'}">${data.status_text}</span></td></tr>
                            <tr><td><strong>Priority:</strong></td><td>${priorityBadge}</td></tr>
                        </table>
                        
                        <h5>Timestamps</h5>
                        <table class="table table-bordered">
                            <tr><td><strong>Created:</strong></td><td>${data.created_at}<br><small class="text-muted">${data.time_since_created}</small></td></tr>
                            <tr><td><strong>Distributed:</strong></td><td>${data.distributed_at}<br><small class="text-muted">${data.time_since_distributed}</small></td></tr>
                        </table>
                        
                        ${data.is_overdue ? `
                        <div class="alert alert-warning">
                            <strong><i class="voyager-exclamation"></i> Overdue:</strong> This revenue share is overdue for distribution.
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
            
            $('#revenueModalBody').html(html);
        })
        .fail(function() {
            $('#revenueModalBody').html(`
                <div class="alert alert-danger">
                    <strong>Error!</strong> Failed to load revenue details.
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

/* Overdue row highlighting */
.table > tbody > tr.warning > td {
    background-color: #fcf8e3;
}

/* Priority colors */
.text-danger {
    color: #d9534f !important;
}

.text-warning {
    color: #f0ad4e !important;
}

.text-info {
    color: #5bc0de !important;
}

.text-success {
    color: #5cb85c !important;
}
</style>
@stop