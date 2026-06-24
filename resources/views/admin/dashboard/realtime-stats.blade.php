@extends('voyager::master')

@section('page_title', 'Realtime Dashboard Stats')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-activity"></i> Realtime Dashboard Stats
        </h1>
        <p class="page-description">Live operational counters used by the admin dashboard widgets.</p>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        @include('admin.dashboard.partials.section-nav')

        @if($error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endif

        <div class="row">
            <div class="col-md-4">
                <div class="panel widget center bgimage" style="background-color:#e67e22;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>{{ number_format($stats['pending_distributions'] ?? 0) }}</h4>
                        <p>Pending Distributions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel widget center bgimage" style="background-color:#e74c3c;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>{{ number_format($stats['overdue_distributions'] ?? 0) }}</h4>
                        <p>Overdue Distributions</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel widget center bgimage" style="background-color:#3498db;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>{{ number_format($stats['active_wallets'] ?? 0) }}</h4>
                        <p>Active Wallets</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-bordered">
                    <div class="panel-body text-center">
                        <h3>${{ number_format($stats['total_wallet_balance_usd'] ?? 0, 2) }}</h3>
                        <p class="text-muted">Total Wallet Balance USD</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-bordered">
                    <div class="panel-body text-center">
                        <h3>{{ number_format($stats['platform_health_score'] ?? 0) }}%</h3>
                        <p class="text-muted">Platform Health Score</p>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-bordered">
                    <div class="panel-body text-center">
                        <h3>{{ number_format($stats['new_users_today'] ?? 0) }}</h3>
                        <p class="text-muted">New Users Today</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="panel panel-bordered">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="voyager-clock"></i> Refresh Details</h3>
            </div>
            <div class="panel-body">
                <p><strong>Last updated:</strong> {{ $stats['last_updated'] ?? now()->format('H:i:s') }}</p>
                <p class="text-muted">This page is the browser-facing version of the realtime stats endpoint. JavaScript requests still receive JSON.</p>
            </div>
        </div>
    </div>
@stop
