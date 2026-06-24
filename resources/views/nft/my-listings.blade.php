@extends('layouts.generic')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-4">My Listings</h1>
            <p class="lead text-muted">NFTs you've listed for sale</p>
        </div>
    </div>

    <div class="row">
        @forelse($listings as $listing)
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100 shadow-sm">
                    <div class="nft-image-container">
                        <img src="{{ $listing->nft->image_url ?? asset('img/default-nft.png') }}" 
                             class="card-img-top nft-image" 
                             alt="{{ $listing->nft->name }}">
                        <div class="nft-price-badge">
                            <i class="fab fa-ethereum"></i> {{ number_format($listing->price, 4) }} ETH
                        </div>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">{{ $listing->nft->name }}</h5>
                        <p class="card-text text-muted small">
                            Status: <span class="badge badge-{{ $listing->status === 'active' ? 'success' : 'secondary' }}">
                                {{ ucfirst($listing->status) }}
                            </span>
                        </p>
                        <div class="mt-auto">
                            <a href="{{ route('nft.show', $listing->nft_id) }}" class="btn btn-sm btn-primary btn-block">
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="text-center py-5">
                    <i class="fas fa-tag fa-3x text-muted mb-3"></i>
                    <h3 class="text-muted">No listings yet</h3>
                    <p class="text-muted">List your NFTs for sale to get started!</p>
                    <a href="{{ route('nft.my-nfts') }}" class="btn btn-primary">
                        <i class="fas fa-box"></i> View My NFTs
                    </a>
                </div>
            </div>
        @endforelse
    </div>

    @if($listings->hasPages())
        <div class="row mt-4">
            <div class="col-12 d-flex justify-content-center">
                {{ $listings->links() }}
            </div>
        </div>
    @endif
</div>

<style>
    .nft-image-container {
        position: relative;
        width: 100%;
        padding-top: 100%;
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
    }
</style>
@endsection

