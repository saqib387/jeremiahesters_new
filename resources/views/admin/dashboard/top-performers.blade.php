@extends('voyager::master')

@section('page_title', 'Dashboard Top Performers')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-trophy"></i> Dashboard Top Performers
        </h1>
        <p class="page-description">Highest-value tokens, wallet holders, distributions, and recent large payouts.</p>
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
            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <div class="panel-heading"><h3 class="panel-title">Top Tokens By Market Cap</h3></div>
                    <div class="panel-body table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Token</th><th>Symbol</th><th>Market Cap</th></tr></thead>
                            <tbody>
                                @forelse(($data['top_tokens_by_market_cap'] ?? []) as $token)
                                    <tr>
                                        <td>{{ $token->name }}</td>
                                        <td>{{ $token->symbol }}</td>
                                        <td>${{ number_format($token->market_cap ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted">No tokens found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <div class="panel-heading"><h3 class="panel-title">Top Users By Wallet Value</h3></div>
                    <div class="panel-body table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>User</th><th>Wallets</th><th>Total Value</th></tr></thead>
                            <tbody>
                                @forelse(($data['top_users_by_wallet_value'] ?? []) as $row)
                                    <tr>
                                        <td>{{ optional($row['user'])->name ?? optional($row['user'])->email ?? 'User #' . $row['user_id'] }}</td>
                                        <td>{{ number_format($row['wallet_count'] ?? 0) }}</td>
                                        <td>${{ number_format($row['total_value_usd'] ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-muted">No wallet values found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <div class="panel-heading"><h3 class="panel-title">Most Distributed Tokens</h3></div>
                    <div class="panel-body table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>Token</th><th>Total Distributed</th></tr></thead>
                            <tbody>
                                @forelse(($data['most_distributed_tokens'] ?? []) as $distribution)
                                    <tr>
                                        <td>{{ optional($distribution->cryptocurrency)->name ?? 'Unknown token' }}</td>
                                        <td>${{ number_format($distribution->total_distributed ?? 0, 2) }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="text-muted">No distributed tokens found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="panel panel-bordered">
                    <div class="panel-heading"><h3 class="panel-title">Recent High-Value Distributions</h3></div>
                    <div class="panel-body table-responsive">
                        <table class="table table-hover">
                            <thead><tr><th>User</th><th>Token</th><th>Amount</th><th>Distributed</th></tr></thead>
                            <tbody>
                                @forelse(($data['recent_high_value_distributions'] ?? []) as $distribution)
                                    <tr>
                                        <td>{{ optional($distribution->user)->name ?? optional($distribution->user)->email ?? 'Unknown user' }}</td>
                                        <td>{{ optional($distribution->cryptocurrency)->symbol ?? 'Unknown' }}</td>
                                        <td>${{ number_format($distribution->distribution_amount ?? 0, 2) }}</td>
                                        <td>{{ $distribution->distributed_at ? $distribution->distributed_at->diffForHumans() : 'Unknown' }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="text-muted">No high-value distributions found.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop
