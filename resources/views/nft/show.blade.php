@extends('layouts.generic')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-6 mb-4">
            <div class="card shadow">
                <div class="nft-image-container-large">
                    <img src="{{ $nft->image_url ?? asset('img/default-nft.png') }}" 
                         class="w-100" 
                         alt="{{ $nft->name }}">
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body">
                    <h1 class="h2 mb-3">{{ $nft->name }}</h1>
                    
                    @if($nft->description)
                        <p class="text-muted mb-4">{{ $nft->description }}</p>
                    @endif

                    <div class="mb-4">
                        <h5>Owner</h5>
                        <div class="d-flex align-items-center">
                            <img src="{{ $nft->user->avatar ?? asset('img/default-avatar.png') }}" 
                                 alt="{{ $nft->user->name }}" 
                                 class="rounded-circle mr-2" 
                                 width="40" height="40">
                            <span>{{ $nft->user->name }}</span>
                        </div>
                    </div>

                    @if($listing && $listing->status === 'active')
                        <div class="border-top border-bottom py-4 mb-4">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <span class="text-muted">Current Price</span>
                                <h3 class="mb-0">
                                    <i class="fab fa-ethereum"></i> {{ number_format($listing->price, 4) }} ETH
                                </h3>
                            </div>
                            <small class="text-muted">Listing fee: {{ number_format($listing->listing_price, 4) }} ETH</small>
                        </div>

                        @if($listing->seller_id !== Auth::id())
                            <form id="buy-nft-form" action="{{ route('nft.buy', $listing->id) }}" method="POST">
                                @csrf
                                <div class="form-group">
                                    <label for="wallet_address">Your Wallet Address</label>
                                    <div class="input-group">
                                        <input type="text" class="form-control" 
                                               id="wallet_address" name="wallet_address" 
                                               placeholder="0x..." required>
                                        <div class="input-group-append">
                                            <button type="button" class="btn btn-outline-primary" id="connect-wallet-btn">
                                                <i class="fab fa-ethereum"></i> Connect
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="transaction_hash">Transaction Hash</label>
                                    <input type="text" class="form-control" 
                                           id="transaction_hash" name="transaction_hash" 
                                           placeholder="0x..." required>
                                    <small class="form-text text-muted">
                                        Complete the purchase transaction in MetaMask and paste the hash here.
                                    </small>
                                </div>
                                <button type="submit" class="btn btn-primary btn-lg btn-block">
                                    <i class="fas fa-shopping-cart"></i> Buy NFT
                                </button>
                            </form>
                        @else
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle"></i> This is your NFT listing.
                            </div>
                        @endif
                    @else
                        <div class="alert alert-secondary">
                            <i class="fas fa-info-circle"></i> This NFT is not currently listed for sale.
                        </div>
                        @if($nft->user_id === Auth::id())
                            <a href="{{ route('nft.resell', $nft->id) }}" class="btn btn-primary btn-lg btn-block">
                                <i class="fas fa-tag"></i> List for Sale
                            </a>
                        @endif
                    @endif

                    <div class="mt-4 pt-4 border-top">
                        <h5>Details</h5>
                        <table class="table table-sm">
                            <tr>
                                <td>Token ID:</td>
                                <td><code>{{ $nft->token_id }}</code></td>
                            </tr>
                            <tr>
                                <td>Status:</td>
                                <td><span class="badge badge-{{ $nft->status === 'sold' ? 'success' : 'primary' }}">{{ ucfirst($nft->status) }}</span></td>
                            </tr>
                            <tr>
                                <td>Created:</td>
                                <td>{{ $nft->created_at->format('M d, Y') }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="{{ asset('js/web3.js') }}"></script>
<script>
    // Connect MetaMask
    document.getElementById('connect-wallet-btn')?.addEventListener('click', async function() {
        if (typeof window.ethereum !== 'undefined') {
            try {
                const accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
                if (accounts.length > 0) {
                    document.getElementById('wallet_address').value = accounts[0];
                    this.innerHTML = '<i class="fas fa-check"></i> Connected';
                    this.classList.remove('btn-outline-primary');
                    this.classList.add('btn-success');
                }
            } catch (error) {
                alert('Error connecting to MetaMask: ' + error.message);
            }
        } else {
            alert('MetaMask is not installed. Please install MetaMask to continue.');
        }
    });
</script>

<style>
    .nft-image-container-large {
        width: 100%;
        padding-top: 100%;
        position: relative;
        overflow: hidden;
        background: #f8f9fa;
    }

    .nft-image-container-large img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>
@endsection

