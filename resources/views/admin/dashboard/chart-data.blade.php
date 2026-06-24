@extends('voyager::master')

@section('page_title', 'Dashboard Chart Data')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-bar-chart"></i> Dashboard Chart Data
        </h1>
        <p class="page-description">Revenue, wallet, token, and distribution trends for the last {{ $days }} days.</p>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')
        @include('admin.dashboard.partials.section-nav')

        @if($error)
            <div class="alert alert-danger">{{ $error }}</div>
        @endif

        @foreach($charts as $chartName => $chartRows)
            <div class="panel panel-bordered">
                <div class="panel-heading">
                    <h3 class="panel-title">{{ ucwords(str_replace('_', ' ', $chartName)) }}</h3>
                </div>
                <div class="panel-body table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Date</th>
                                @if($chartName === 'revenue')
                                    <th>Total Revenue</th>
                                @elseif($chartName === 'distributions')
                                    <th>Distribution Count</th>
                                    <th>Total Distributed</th>
                                @else
                                    <th>Count</th>
                                @endif
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($chartRows as $row)
                                <tr>
                                    <td>{{ $row->date ?? 'Unknown' }}</td>
                                    @if($chartName === 'revenue')
                                        <td>${{ number_format($row->total ?? 0, 2) }}</td>
                                    @elseif($chartName === 'distributions')
                                        <td>{{ number_format($row->count ?? 0) }}</td>
                                        <td>${{ number_format($row->total_amount ?? 0, 2) }}</td>
                                    @else
                                        <td>{{ number_format($row->count ?? 0) }}</td>
                                    @endif
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-muted">No chart data found for this period.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        @endforeach
    </div>
@stop
