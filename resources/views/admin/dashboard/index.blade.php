@extends('voyager::master')

@section('page_title', 'Platform Dashboard')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <h1 class="page-title">
                    <i class="voyager-dashboard"></i> Platform Dashboard
                </h1>
                <p class="page-description">Overview of your cryptocurrency platform</p>
            </div>
            <div class="col-md-4 text-right">
                <div class="btn-group">
                    <button class="btn btn-primary" onclick="refreshDashboard()">
                        <i class="voyager-refresh"></i> Refresh
                    </button>
                    <button class="btn btn-success" onclick="exportSummary()">
                        <i class="voyager-download"></i> Export
                    </button>
                </div>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        @include('admin.dashboard.partials.section-nav')
        
        <!-- Platform Health Banner -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none;">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-8">
                                <h3 style="color: white; margin-top: 0;">
                                    <i class="voyager-activity"></i> Platform Health Score: 
                                    <span id="health-score">{{ $overviewStats['platform_health_score'] ?? 85 }}</span>%
                                </h3>
                                <p style="color: rgba(255,255,255,0.8);">
                                    Real-time monitoring of your cryptocurrency platform performance
                                    <span id="last-updated" style="font-size: 12px;">• Last updated: {{ now()->format('H:i:s') }}</span>
                                </p>
                            </div>
                            <div class="col-md-4 text-right">
                                <div style="font-size: 24px; margin-top: 10px;">
                                    <i class="voyager-trophy" style="margin-right: 10px;"></i>
                                    <span style="font-size: 14px;">Total Platform Value</span><br>
                                    <strong>${{ number_format(($quickStats['total_platform_value'] ?? 0), 2) }}</strong>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Key Metrics Cards -->
        <div class="row">
            <!-- Users & Platform -->
            <div class="col-md-3">
                <div class="panel widget center bgimage" style="background-color:#3498db;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4 id="total-users">{{ number_format($overviewStats['total_users'] ?? 0) }}</h4>
                        <p>Total Users</p>
                        <small>+{{ $overviewStats['new_users_today'] ?? 0 }} today</small>
                    </div>
                </div>
            </div>
            
            <!-- Tokens -->
            <div class="col-md-3">
                <div class="panel widget center bgimage" style="background-color:#2ecc71;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>{{ number_format($overviewStats['total_tokens'] ?? 0) }}</h4>
                        <p>Total Tokens</p>
                        <small>{{ $overviewStats['verified_tokens'] ?? 0 }} verified</small>
                    </div>
                </div>
            </div>
            
            <!-- Wallets -->
            <div class="col-md-3">
                <div class="panel widget center bgimage" style="background-color:#f39c12;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4 id="active-wallets">{{ number_format($overviewStats['active_wallets'] ?? 0) }}</h4>
                        <p>Active Wallets</p>
                        <small>${{ number_format($overviewStats['total_wallet_balance_usd'] ?? 0, 0) }} total value</small>
                    </div>
                </div>
            </div>
            
            <!-- Revenue -->
            <div class="col-md-3">
                <div class="panel widget center bgimage" style="background-color:#e74c3c;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4 id="pending-distributions">{{ number_format($overviewStats['pending_distributions'] ?? 0) }}</h4>
                        <p>Pending Distributions</p>
                        <small id="overdue-count">{{ $overviewStats['overdue_distributions'] ?? 0 }} overdue</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Alerts Section -->
        @if(!empty($alerts))
        <div class="row" id="platform-alerts-row">
            <div class="col-md-12">
                <div class="panel panel-bordered" id="platform-alerts-panel">
                    <div class="panel-heading">
                        <h3 class="panel-title platform-alerts-title">
                            <i class="voyager-warning"></i> Platform Alerts
                        </h3>
                    </div>
                    <div class="panel-body platform-alerts-body">
                        @foreach($alerts as $alert)
                            <div class="alert alert-{{ $alert['type'] }} alert-dismissible platform-alert-item">
                                <button type="button" class="close" data-dismiss="alert">&times;</button>
                                <strong>{{ $alert['title'] }}</strong> {{ $alert['message'] }}
                                @if($alert['action_url'])
                                    <a href="{{ $alert['action_url'] }}" class="btn btn-sm btn-{{ $alert['type'] }} pull-right">
                                        {{ $alert['action_text'] }}
                                    </a>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Quick Actions & Recent Activity -->
        <div class="row">
            <!-- Quick Actions -->
            <div class="col-md-4">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="voyager-rocket"></i> Quick Actions
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="list-group">
                            <a href="{{ route('voyager.tokens.index') }}" class="list-group-item">
                                <i class="voyager-trophy text-primary"></i>
                                <strong>Manage Tokens</strong>
                                <span class="pull-right">{{ $overviewStats['total_tokens'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('voyager.wallets.index') }}" class="list-group-item">
                                <i class="voyager-wallet text-success"></i>
                                <strong>View Wallets</strong>
                                <span class="pull-right">{{ $overviewStats['total_wallets'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('voyager.revenue.index', ['status' => 'pending']) }}" class="list-group-item">
                                <i class="voyager-dollar text-warning"></i>
                                <strong>Pending Distributions</strong>
                                <span class="pull-right">{{ $overviewStats['pending_distributions'] ?? 0 }}</span>
                            </a>
                            <a href="{{ route('voyager.revenue.index', ['status' => 'overdue']) }}" class="list-group-item">
                                <i class="voyager-exclamation text-danger"></i>
                                <strong>Overdue Distributions</strong>
                                <span class="pull-right">{{ $overviewStats['overdue_distributions'] ?? 0 }}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Tokens -->
            <div class="col-md-4">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="voyager-trophy"></i> Recent Tokens
                        </h3>
                    </div>
                    <div class="panel-body">
                        @if(!empty($recentActivity['recent_tokens']) && $recentActivity['recent_tokens']->count() > 0)
                            @foreach($recentActivity['recent_tokens'] as $token)
                                <div class="media" style="margin-bottom: 10px;">
                                    @if($token->logo)
                                        <div class="media-left">
                                            <img src="{{ Storage::url($token->logo) }}" 
                                                 alt="{{ $token->name }}" 
                                                 style="width: 30px; height: 30px; border-radius: 50%;">
                                        </div>
                                    @endif
                                    <div class="media-body">
                                        <strong>{{ $token->name }}</strong>
                                        <small class="text-muted">({{ $token->symbol }})</small>
                                        @if($token->is_verified)
                                            <i class="voyager-check text-success"></i>
                                        @endif
                                        <br>
                                        <small class="text-muted">
                                            {{ $token->created_at ? $token->created_at->diffForHumans() : 'Unknown' }}
                                        </small>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No recent tokens found.</p>
                        @endif
                        <div class="text-center" style="margin-top: 10px;">
                            <a href="{{ route('voyager.tokens.index') }}" class="btn btn-sm btn-primary">
                                View All Tokens
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Distributions -->
            <div class="col-md-4">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="voyager-dollar"></i> Recent Distributions
                        </h3>
                    </div>
                    <div class="panel-body">
                        @if(!empty($recentActivity['recent_distributions']) && $recentActivity['recent_distributions']->count() > 0)
                            @foreach($recentActivity['recent_distributions'] as $distribution)
                                <div style="margin-bottom: 10px; padding: 8px; border-left: 3px solid #2ecc71;">
                                    <strong>${{ number_format($distribution->distribution_amount, 2) }}</strong>
                                    @if($distribution->cryptocurrency)
                                        <small>({{ $distribution->cryptocurrency->symbol }})</small>
                                    @endif
                                    <br>
                                    @if($distribution->user)
                                        <small class="text-muted">{{ $distribution->user->name }}</small><br>
                                    @endif
                                    <small class="text-success">
                                        <i class="voyager-check"></i>
                                        {{ $distribution->distributed_at ? $distribution->distributed_at->diffForHumans() : 'Recently' }}
                                    </small>
                                </div>
                            @endforeach
                        @else
                            <p class="text-muted">No recent distributions found.</p>
                        @endif
                        <div class="text-center" style="margin-top: 10px;">
                            <a href="{{ route('voyager.revenue.index', ['status' => 'distributed']) }}" class="btn btn-sm btn-success">
                                View All Distributions
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Performance Metrics -->
        <div class="row">
            <!-- Market Overview -->
            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="voyager-bar-chart"></i> Market Overview
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="metric-box text-center">
                                    <h4 class="text-primary">${{ number_format($overviewStats['total_market_cap'] ?? 0, 0) }}</h4>
                                    <p class="text-muted">Total Market Cap</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="metric-box text-center">
                                    <h4 class="text-success">${{ number_format($overviewStats['total_distributed_amount'] ?? 0, 0) }}</h4>
                                    <p class="text-muted">Total Distributed</p>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="metric-box text-center">
                                    <h4 class="text-info">{{ number_format($quickStats['active_users_percentage'] ?? 0, 1) }}%</h4>
                                    <p class="text-muted">Active Users</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="metric-box text-center">
                                    <h4 class="text-warning">{{ number_format($quickStats['distribution_efficiency'] ?? 0, 1) }}%</h4>
                                    <p class="text-muted">Distribution Efficiency</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- High Priority Items -->
            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            <i class="voyager-exclamation"></i> High Priority Items
                        </h3>
                    </div>
                    <div class="panel-body">
                        @if(!empty($recentActivity['pending_high_priority']) && $recentActivity['pending_high_priority']->count() > 0)
                            @foreach($recentActivity['pending_high_priority'] as $priority)
                                <div style="margin-bottom: 10px; padding: 8px; border-left: 3px solid #e74c3c;">
                                    <strong>${{ number_format($priority->distribution_amount, 2) }}</strong>
                                    @if($priority->cryptocurrency)
                                        <small>({{ $priority->cryptocurrency->symbol }})</small>
                                    @endif
                                    @if($priority->created_at && $priority->created_at->diffInDays(now()) > 30)
                                        <span class="label label-danger">OVERDUE</span>
                                    @elseif($priority->distribution_amount > 1000)
                                        <span class="label label-warning">HIGH VALUE</span>
                                    @endif
                                    <br>
                                    @if($priority->user)
                                        <small class="text-muted">{{ $priority->user->name }}</small><br>
                                    @endif
                                    <small class="text-danger">
                                        <i class="voyager-clock"></i>
                                        {{ $priority->created_at ? $priority->created_at->diffForHumans() : 'Unknown date' }}
                                    </small>
                                </div>
                            @endforeach
                        @else
                            <div class="text-center">
                                <i class="voyager-check text-success" style="font-size: 48px;"></i>
                                <p class="text-success"><strong>All Clear!</strong></p>
                                <p class="text-muted">No high priority items requiring attention.</p>
                            </div>
                        @endif
                        @if(!empty($recentActivity['pending_high_priority']) && $recentActivity['pending_high_priority']->count() > 0)
                            <div class="text-center" style="margin-top: 10px;">
                                <a href="{{ route('voyager.revenue.index', ['status' => 'overdue']) }}" class="btn btn-sm btn-danger">
                                    View All High Priority
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer Info -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body text-center" style="background-color: #f8f9fa;">
                        <small class="text-muted">
                            Dashboard last refreshed: <span id="refresh-time">{{ now()->format('M d, Y H:i:s') }}</span> | 
                            Auto-refresh: <span id="auto-refresh-status">Enabled</span> |
                            <a href="#" onclick="toggleAutoRefresh()">Toggle Auto-refresh</a>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
<script>
let autoRefreshEnabled = true;
let refreshInterval;

$(document).ready(function() {
    // Start auto-refresh
    startAutoRefresh();
    
    // Initialize tooltips
    $('[data-toggle="tooltip"]').tooltip();

    // Collapse Platform Alerts section when every alert is dismissed (Bootstrap 3)
    $(document).on('closed.bs.alert', '.platform-alert-item', function() {
        setTimeout(function() {
            var $panel = $('#platform-alerts-panel');
            if ($panel.length && $panel.find('.platform-alert-item').length === 0) {
                $('#platform-alerts-row').remove();
            }
        }, 0);
    });
});

function startAutoRefresh() {
    if (autoRefreshEnabled) {
        refreshInterval = setInterval(function() {
            updateRealtimeStats();
        }, 30000); // Refresh every 30 seconds
    }
}

function stopAutoRefresh() {
    if (refreshInterval) {
        clearInterval(refreshInterval);
    }
}

function toggleAutoRefresh() {
    autoRefreshEnabled = !autoRefreshEnabled;
    $('#auto-refresh-status').text(autoRefreshEnabled ? 'Enabled' : 'Disabled');
    
    if (autoRefreshEnabled) {
        startAutoRefresh();
    } else {
        stopAutoRefresh();
    }
}

function updateRealtimeStats() {
    $.get('{{ route("voyager.dashboard.realtime-stats") }}')
        .done(function(data) {
            $('#pending-distributions').text(data.pending_distributions.toLocaleString());
            $('#overdue-count').text(data.overdue_distributions + ' overdue');
            $('#active-wallets').text(data.active_wallets.toLocaleString());
            $('#health-score').text(data.platform_health_score);
            $('#last-updated').text('• Last updated: ' + data.last_updated);
            $('#refresh-time').text(new Date().toLocaleString());
        })
        .fail(function() {
            console.log('Failed to fetch real-time stats');
        });
}

function refreshDashboard() {
    updateRealtimeStats();
    toastr.success('Dashboard refreshed successfully!');
}

function exportSummary() {
    window.location.href = '{{ route("voyager.dashboard.export") }}';
    toastr.info('Exporting dashboard summary...');
}

// Add some visual feedback
$('.panel.widget').hover(
    function() { $(this).addClass('animated pulse'); },
    function() { $(this).removeClass('animated pulse'); }
);
</script>

<style>
.metric-box {
    padding: 15px;
    margin-bottom: 15px;
}

.metric-box h4 {
    margin: 0 0 5px 0;
    font-weight: bold;
}

.metric-box p {
    margin: 0;
    font-size: 12px;
}

.panel.widget {
    transition: transform 0.2s;
}

.panel.widget:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

.list-group-item {
    border: none;
    padding: 10px 15px;
    border-bottom: 1px solid #f0f0f0;
}

.list-group-item:hover {
    background-color: #f8f9fa;
}

.alert {
    margin-bottom: 10px;
}

/* Tighten heading → first alert; hide empty panel when all alerts dismissed */
.platform-alerts-body {
    padding-top: 10px;
}
.platform-alerts-body .platform-alert-item:first-child {
    margin-top: 0;
}

.media {
    align-items: center;
}

@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.05); }
    100% { transform: scale(1); }
}

.animated.pulse {
    animation: pulse 1s;
}
</style>
@stop
