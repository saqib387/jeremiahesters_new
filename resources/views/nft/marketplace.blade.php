@extends('layouts.generic')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="display-4 mb-2">NFT Marketplace</h1>
                    <p class="lead text-muted">Discover and collect unique digital assets</p>
                </div>
                <a href="{{ route('nft.create') }}" class="btn btn-primary btn-lg">
                    <i class="fas fa-plus"></i> Create NFT
                </a>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- NFT Grid -->
    <div class="row">
        @forelse($listings as $listing)
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100 shadow-sm nft-card">
                    <div class="nft-image-container">
                        <img src="{{ $listing->nft->image_url ?? asset('img/default-nft.png') }}" 
                             class="card-img-top nft-image" 
                             alt="{{ $listing->nft->name }}">
                        <div class="nft-price-badge">
                            {{ rtrim(rtrim(number_format($listing->price, 6), '0'), '.') }} {{ strtoupper(config('web3.network')) }}
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $listing->nft->name }}</h5>
                        <p class="card-text text-muted small flex-grow-1">
                            {{ Str::limit($listing->nft->description ?? 'No description', 100) }}
                        </p>
                        <div class="small text-muted mb-2">
                            <i class="fas fa-user"></i> {{ $listing->seller->name ?? 'Unknown' }}
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-auto">
                            <a href="{{ route('nft.show', $listing->nft_id) }}" class="btn btn-sm btn-outline-primary">View</a>
                            @if((int) $listing->seller_id !== (int) auth()->id() && auth()->user()->wallet_address)
                                <form action="{{ route('nft.buy', $listing->id) }}" method="POST"
                                      onsubmit="return confirm('Buy this NFT for {{ $listing->price }} {{ strtoupper(config('web3.network')) }}?');">
                                    @csrf
                                    <button class="btn btn-sm btn-primary"><i class="fas fa-shopping-cart"></i> Buy</button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-box-open fa-3x text-muted mb-3"></i>
                    <h3 class="text-muted">No NFTs available</h3>
                    <p class="text-muted">Be the first to create and list an NFT!</p>
                    <a href="{{ route('nft.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Create NFT
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($listings->hasPages())
        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-center">
                {{ $listings->links() }}
            </div>
        </div>
    @endif
</div>

<style>
    .nft-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        border: none;
        overflow: hidden;
    }

    .nft-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.15) !important;
    }

    .nft-image-container {
        position: relative;
        width: 100%;
        padding-top: 100%; /* 1:1 Aspect Ratio */
        overflow: hidden;
        background: #f8f9fa;
    }

    .nft-image {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .nft-price-badge {
        position: absolute;
        bottom: 10px;
        right: 10px;
        background: rgba(0, 0, 0, 0.8);
        color: white;
        padding: 8px 12px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.9rem;
        backdrop-filter: blur(10px);
    }

    .nft-price-badge i {
        margin-right: 5px;
    }
</style>
@endsection

