@extends('voyager::master')

@section('page_title', 'View Token')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-eye"></i> Token Details: {{ $cryptocurrency->name }}
        </h1>
        <div class="btn-group pull-right">
            <a href="{{ route('voyager.tokens.edit', $cryptocurrency->id) }}" class="btn btn-warning">
                <i class="voyager-edit"></i> <span>Edit Token</span>
            </a>
            <a href="{{ route('voyager.tokens.index') }}" class="btn btn-default">
                <i class="voyager-list"></i> <span>Back to Tokens</span>
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <!-- Left Column - Basic Info -->
                            <div class="col-md-8">
                                <!-- Basic Information -->
                                <div class="panel panel-bordered panel-info">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Basic Information</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Token Name:</strong><br>
                                                {{ $cryptocurrency->name }}
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Symbol:</strong><br>
                                                {{ $cryptocurrency->symbol }}
                                                @if($cryptocurrency->is_verified)
                                                    <i class="voyager-check text-success" title="Verified"></i>
                                                @endif
                                            </div>
                                        </div>
                                        <br>
                                        
                                        @if($cryptocurrency->description)
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <strong>Description:</strong><br>
                                                    {{ $cryptocurrency->description }}
                                                </div>
                                            </div>
                                            <br>
                                        @endif
                                        
                                        <div class="row">
                                            @if($cryptocurrency->website)
                                                <div class="col-md-6">
                                                    <strong>Website:</strong><br>
                                                    <a href="{{ $cryptocurrency->website }}" target="_blank">{{ $cryptocurrency->website }}</a>
                                                </div>
                                            @endif
                                            @if($cryptocurrency->whitepaper)
                                                <div class="col-md-6">
                                                    <strong>Whitepaper:</strong><br>
                                                    <a href="{{ $cryptocurrency->whitepaper }}" target="_blank">View Whitepaper</a>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Price & Market Data -->
                                <div class="panel panel-bordered panel-success">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Price & Market Data</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Current Price:</strong><br>
                                                ${{ number_format($cryptocurrency->current_price, 8) }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Initial Price:</strong><br>
                                                ${{ number_format($cryptocurrency->initial_price, 8) }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>24h Change:</strong><br>
                                                <span class="{{ $cryptocurrency->price_change_color }}">
                                                    <i class="{{ $cryptocurrency->price_change_icon }}"></i>
                                                    {{ number_format($cryptocurrency->change_24h, 2) }}%
                                                </span>
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Market Cap:</strong><br>
                                                ${{ number_format((float) $cryptocurrency->market_cap, 2) }}
                                            </div>
                                        </div>
                                        <br>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>24h Volume:</strong><br>
                                                ${{ number_format((float) $cryptocurrency->volume_24h, 2) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Supply Information -->
                                <div class="panel panel-bordered panel-warning">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Supply Information</h3>
                                    </div>
                                    <div class="panel-body">
                                        @php
                                            $maxS = $cryptocurrency->max_supply;
                                            $circS = $cryptocurrency->circulating_supply;
                                            $supplyInvalid = $maxS !== null && (float) $circS > (float) $maxS;
                                        @endphp
                                        @if($supplyInvalid)
                                            <div class="alert alert-warning">
                                                Data issue: circulating supply is greater than max supply. Edit the token to correct values.
                                            </div>
                                        @endif
                                        <div class="row">
                                            <div class="col-md-3">
                                                <strong>Total Supply:</strong><br>
                                                {{ number_format($cryptocurrency->total_supply, 2) }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Available Supply:</strong><br>
                                                {{ number_format($cryptocurrency->available_supply, 2) }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Circulating Supply:</strong><br>
                                                {{ number_format($cryptocurrency->circulating_supply, 2) }}
                                            </div>
                                            <div class="col-md-3">
                                                <strong>Max Supply:</strong><br>
                                                {{ $cryptocurrency->max_supply ? number_format($cryptocurrency->max_supply, 2) : 'Unlimited' }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Blockchain & Contract -->
                                <div class="panel panel-bordered panel-primary">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Blockchain & Contract</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="row">
                                            <div class="col-md-6">
                                                <strong>Blockchain Network:</strong><br>
                                                <span class="label label-default">{{ $cryptocurrency->display_network_label }}</span>
                                            </div>
                                            <div class="col-md-6">
                                                <strong>Token Type:</strong><br>
                                                <span class="label label-info">{{ ucfirst($cryptocurrency->token_type) }}</span>
                                            </div>
                                        </div>
                                        <br>
                                        
                                        @if($cryptocurrency->contract_address)
                                            <div class="row">
                                                <div class="col-md-12">
                                                    <strong>Contract Address:</strong><br>
                                                    <code>{{ $cryptocurrency->contract_address }}</code>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Right Column - Settings & Status -->
                            <div class="col-md-4">
                                <!-- Logo & Basic Info -->
                                <div class="panel panel-bordered">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Token Logo</h3>
                                    </div>
                                    <div class="panel-body text-center">
                                        @if($cryptocurrency->logo)
                                            <img src="{{ Storage::url($cryptocurrency->logo) }}" 
                                                 alt="{{ $cryptocurrency->name }}" 
                                                 style="width: 150px; height: 150px; border-radius: 50%;">
                                        @else
                                            <div style="width: 150px; height: 150px; border-radius: 50%; background-color: #f0f0f0; display: flex; align-items: center; justify-content: center; margin: 0 auto;">
                                                <span style="font-size: 48px; color: #ccc;">{{ substr($cryptocurrency->symbol, 0, 2) }}</span>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Status & Features -->
                                <div class="panel panel-bordered panel-success">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Status & Features</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <strong>Status:</strong><br>
                                            @if($cryptocurrency->is_active)
                                                <span class="label label-success">Active</span>
                                            @else
                                                <span class="label label-danger">Inactive</span>
                                            @endif
                                            
                                            @if($cryptocurrency->is_verified)
                                                <span class="label label-info">Verified</span>
                                            @endif
                                            
                                            @if(!$cryptocurrency->transferable)
                                                <span class="label label-warning">Frozen</span>
                                            @endif
                                        </div>
                                        
                                        <div class="form-group">
                                            <strong>Features:</strong><br>
                                            @if($cryptocurrency->enable_burning)
                                                <span class="label label-default">Burning Enabled</span><br>
                                            @endif
                                            @if($cryptocurrency->enable_minting)
                                                <span class="label label-default">Minting Enabled</span><br>
                                            @endif
                                            @if($cryptocurrency->transferable)
                                                <span class="label label-default">Transferable</span><br>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Fee Structure -->
                                <div class="panel panel-bordered panel-info">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Fee Structure</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <strong>Creator Fee:</strong><br>
                                            {{ number_format($cryptocurrency->creator_fee_percentage, 2) }}%
                                        </div>
                                        <div class="form-group">
                                            <strong>Platform Fee:</strong><br>
                                            {{ number_format($cryptocurrency->platform_fee_percentage, 2) }}%
                                        </div>
                                        <div class="form-group">
                                            <strong>Liquidity Pool:</strong><br>
                                            {{ number_format($cryptocurrency->liquidity_pool_percentage, 2) }}%
                                        </div>
                                        <div class="form-group">
                                            <strong>Total Fees:</strong><br>
                                            <strong>{{ number_format($cryptocurrency->total_fees, 2) }}%</strong>
                                        </div>
                                    </div>
                                </div>

                                <!-- Creator Information -->
                                @if($cryptocurrency->creator)
                                    <div class="panel panel-bordered">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Creator</h3>
                                        </div>
                                        <div class="panel-body">
                                            <strong>Name:</strong><br>
                                            {{ $cryptocurrency->creator->name }}<br><br>
                                            <strong>Email:</strong><br>
                                            {{ $cryptocurrency->creator->email }}
                                        </div>
                                    </div>
                                @endif

                                <!-- Timestamps -->
                                <div class="panel panel-bordered">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Timestamps</h3>
                                    </div>
                                    <div class="panel-body">
                                        <strong>Created:</strong><br>
                                        {{ $cryptocurrency->created_at ? $cryptocurrency->created_at->format('M d, Y H:i') : 'N/A' }}<br><br>
                                        <strong>Last Updated:</strong><br>
                                        {{ $cryptocurrency->updated_at ? $cryptocurrency->updated_at->format('M d, Y H:i') : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop