@extends('layouts.generic')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-4">Cryptocurrency Explorer</h1>
            <p class="lead">Discover and explore all creator tokens on the platform</p>
        </div>
    </div>
    
    <!-- Search and Filter -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('cryptocurrency.explorer') }}">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group mb-0">
                                    <div class="input-group">
                                        <input type="text" class="form-control" placeholder="Search tokens..." name="search" value="{{ request('search') }}">
                                        <div class="input-group-append">
                                            <button class="btn btn-primary" type="submit">
                                                <i class="fas fa-search"></i> Search
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="d-flex justify-content-end">
                                    <div class="form-group mb-0 mr-2">
                                        <select class="form-control" name="sort" onchange="this.form.submit()">
                                            <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>Newest First</option>
                                            <option value="current_price" {{ request('sort') == 'current_price' ? 'selected' : '' }}>Price (High to Low)</option>
                                            <option value="name" {{ request('sort') == 'name' ? 'selected' : '' }}>Name (A-Z)</option>
                                        </select>
                                    </div>
                                    <div class="form-group mb-0">
                                        <select class="form-control" name="blockchain" onchange="this.form.submit()">
                                            <option value="">All Networks</option>
                                            <option value="ethereum" {{ request('blockchain') == 'ethereum' ? 'selected' : '' }}>Ethereum</option>
                                            <option value="binance" {{ request('blockchain') == 'binance' ? 'selected' : '' }}>Binance Smart Chain</option>
                                            <option value="polygon" {{ request('blockchain') == 'polygon' ? 'selected' : '' }}>Polygon</option>
                                            <option value="solana" {{ request('blockchain') == 'solana' ? 'selected' : '' }}>Solana</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Cryptocurrencies Grid -->
    <div class="row">
        @foreach($cryptocurrencies as $crypto)
        <div class="col-md-4 col-lg-3 mb-4">
            <div class="card h-100 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        @if($crypto->logo)
                            <img src="{{ asset('storage/' . $crypto->logo) }}" alt="{{ $crypto->name }}" class="rounded-circle mr-3" width="48" height="48">
                        @else
                            <div class="rounded-circle bg-primary text-white mr-3 d-flex align-items-center justify-content-center" style="width: 48px; height: 48px;">
                                <span class="h4 mb-0">{{ substr($crypto->symbol, 0, 1) }}</span>
                            </div>
                        @endif
                        <div>
                            <h5 class="mb-0">{{ $crypto->name }}</h5>
                            <p class="text-muted mb-0">{{ $crypto->symbol }}</p>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-1">
                            <span>Price:</span>
                            <span>${{ number_format($crypto->current_price, 8) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span>Market Cap:</span>
                            <span>${{ number_format($crypto->market_cap, 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span>Network:</span>
                            <span class="text-capitalize">{{ $crypto->blockchain_network }}</span>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <a href="{{ route('profile', $crypto->creator->username) }}" class="text-decoration-none">
                            <div class="d-flex align-items-center">
                                <img src="{{ $crypto->creator->avatar ?? asset('img/default-avatar.png') }}" alt="{{ $crypto->creator->name }}" class="rounded-circle mr-1" width="24" height="24">
                                <small>{{ $crypto->creator->name }}</small>
                            </div>
                        </a>
                    </div>
                    
                    <div class="d-flex">
                        <a href="{{ route('cryptocurrency.show', $crypto->id) }}" class="btn btn-sm btn-outline-primary flex-grow-1 mr-1">Details</a>
                        <a href="{{ route('cryptocurrency.buy.form', $crypto->id) }}" class="btn btn-sm btn-primary flex-grow-1">Buy</a>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Pagination -->
    <div class="row mt-4">
        <div class="col-12 d-flex justify-content-center">
            {{ $cryptocurrencies->appends(request()->query())->links() }}
        </div>
    </div>
    
    <!-- Create Token CTA -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card bg-gradient-primary text-white">
                <div class="card-body text-center py-4">
                    <h2 class="mb-3">Create Your Own Token</h2>
                    <p class="lead mb-4">Launch your own cryptocurrency and let your fans invest in your success</p>
                    <a href="{{ route('cryptocurrency.create') }}" class="btn btn-light btn-lg">Get Started</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 