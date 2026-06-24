@extends('layouts.generic')

@section('page_title', __('My Cryptocurrency Wallet'))

@section('styles')
<style>
    .wallet-balance {
        padding: 20px;
        text-align: center;
    }
    .wallet-card {
        border-radius: 8px;
        transition: transform 0.2s;
        margin-bottom: 20px;
    }
    .wallet-card:hover {
        transform: translateY(-5px);
    }
    .crypto-logo {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        margin-right: 15px;
    }
    .crypto-symbol {
        font-size: 24px;
        font-weight: bold;
        display: flex;
        align-items: center;
        justify-content: center;
        width: 48px;
        height: 48px;
        border-radius: 50%;
        margin-right: 15px;
        background-color: #f0f0f0;
    }
    .wallet-details {
        display: flex;
        align-items: center;
    }
    .empty-state {
        text-align: center;
        padding: 50px 20px;
    }
    .empty-state-icon {
        font-size: 64px;
        margin-bottom: 20px;
        color: #ccc;
    }
</style>
@endsection

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex justify-content-between align-items-center">
                <h2 class="mb-0">{{ __('My Wallet') }}</h2>
                <a href="{{ route('cryptocurrency.index') }}" class="btn btn-outline-primary">
                    {{ __('Explore Tokens') }}
                </a>
            </div>
        </div>
        
        <div class="col-12 col-md-4 col-lg-3 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Total Balance') }}</h5>
                </div>
                <div class="card-body wallet-balance">
                    <h3>${{ number_format($totalValue, 2) }}</h3>
                    <p class="text-muted mb-0">{{ __('Total Value in USD') }}</p>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-8 col-lg-9">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('My Tokens') }}</h5>
                </div>
                <div class="card-body">
                    @if($wallets->isEmpty())
                        <div class="empty-state">
                            <div class="empty-state-icon">
                                <i class="ion-ios-wallet-outline"></i>
                            </div>
                            <h4>{{ __("You don't have any tokens yet") }}</h4>
                            <p class="text-muted">{{ __('Start by buying tokens from content creators you support') }}</p>
                            <a href="{{ route('cryptocurrency.index') }}" class="btn btn-primary mt-3">
                                {{ __('Buy Tokens') }}
                            </a>
                        </div>
                    @else
                        <div class="row">
                            @foreach($wallets as $wallet)
                                <div class="col-12 col-md-6 col-xl-4">
                                    <div class="card wallet-card">
                                        <div class="card-body">
                                            <div class="wallet-details">
                                                @if($wallet->cryptocurrency->logo)
                                                    <img class="crypto-logo" src="{{ Storage::disk('public')->url($wallet->cryptocurrency->logo) }}" alt="{{ $wallet->cryptocurrency->name }}">
                                                @else
                                                    <div class="crypto-symbol">
                                                        {{ strtoupper(substr($wallet->cryptocurrency->symbol, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div>
                                                    <h5 class="mb-0">{{ $wallet->cryptocurrency->name }}</h5>
                                                    <span class="badge badge-light">{{ strtoupper($wallet->cryptocurrency->symbol) }}</span>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <div class="d-flex justify-content-between">
                                                    <span>{{ __('Balance') }}:</span>
                                                    <span>{{ number_format($wallet->balance) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>{{ __('Value') }}:</span>
                                                    <span>${{ number_format($wallet->value, 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span>{{ __('Price') }}:</span>
                                                    <span>${{ number_format($wallet->cryptocurrency->current_price, 8) }}</span>
                                                </div>
                                            </div>
                                            <div class="mt-3">
                                                <a href="{{ route('cryptocurrency.wallet.show', $wallet->id) }}" class="btn btn-outline-primary btn-sm btn-block">
                                                    {{ __('Details') }}
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 