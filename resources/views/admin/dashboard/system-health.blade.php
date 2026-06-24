@extends('voyager::master')

@section('page_title', 'Dashboard System Health')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-tools"></i> Dashboard System Health
        </h1>
        <p class="page-description">Database, activity, data integrity, and performance checks.</p>
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
            @foreach($health as $section => $details)
                @php
                    $status = $details['status'] ?? 'healthy';
                    $label = $status === 'error' ? 'danger' : ($status === 'warning' ? 'warning' : 'success');
                @endphp
                <div class="col-md-6">
                    <div class="panel panel-bordered">
                        <div class="panel-heading">
                            <h3 class="panel-title">
                                {{ ucwords(str_replace('_', ' ', $section)) }}
                                <span class="label label-{{ $label }} pull-right">{{ ucfirst($status) }}</span>
                            </h3>
                        </div>
                        <div class="panel-body">
                            <table class="table table-condensed">
                                <tbody>
                                    @foreach($details as $key => $value)
                                        <tr>
                                            <th style="width: 45%;">{{ ucwords(str_replace('_', ' ', $key)) }}</th>
                                            <td>
                                                @if(is_array($value))
                                                    {{ json_encode($value) }}
                                                @else
                                                    {{ $value }}
                                                @endif
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@stop
