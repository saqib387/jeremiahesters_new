@extends('layouts.generic')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Buy {{ $cryptocurrency->name }} ({{ $cryptocurrency->symbol }})</h2>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="d-flex align-items-center mb-3">
                                @if($cryptocurrency->logo)
                                    <img src="{{ asset('storage/' . $cryptocurrency->logo) }}" alt="{{ $cryptocurrency->name }}" class="rounded-circle mr-3" width="64" height="64">
                                @else
                                    <div class="rounded-circle bg-primary text-white mr-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                        <span class="h3 mb-0">{{ substr($cryptocurrency->symbol, 0, 1) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="mb-0">{{ $cryptocurrency->name }}</h3>
                                    <p class="text-muted mb-0">{{ $cryptocurrency->symbol }}</p>
                                </div>
                            </div>
                            
                            <p>{{ $cryptocurrency->description }}</p>
                            
                            <div class="card bg-light mb-3">
                                <div class="card-body">
                                    <div class="row">
                                        <div class="col-6">
                                            <p class="text-muted mb-1">Current Price</p>
                                            <h4>${{ number_format($cryptocurrency->current_price, 8) }}</h4>
                                        </div>
                                        <div class="col-6">
                                            <p class="text-muted mb-1">Available Supply</p>
                                            <h4>{{ number_format($cryptocurrency->available_supply) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-primary">
                                <div class="card-header bg-primary text-white">
                                    <h4 class="mb-0">Purchase Details</h4>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('cryptocurrency.buy', $cryptocurrency->id) }}">
                                        @csrf
                                        
                                        <div class="form-group">
                                            <label for="amount">Amount of tokens to buy</label>
                                            <input type="number" class="form-control form-control-lg @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', 100) }}" min="1" max="{{ $cryptocurrency->available_supply }}" required>
                                            <div class="form-text">Enter the number of tokens you want to purchase</div>
                                            @error('amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="card bg-light mb-4">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Price per token:</span>
                                                    <span>${{ number_format($cryptocurrency->current_price, 8) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Number of tokens:</span>
                                                    <span id="token-amount">100</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Subtotal:</span>
                                                    <span id="subtotal">${{ number_format($cryptocurrency->current_price * 100, 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Platform fee (2.5%):</span>
                                                    <span id="platform-fee">${{ number_format($cryptocurrency->current_price * 100 * 0.025, 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Creator fee ({{ $cryptocurrency->creator_fee_percentage }}%):</span>
                                                    <span id="creator-fee">${{ number_format($cryptocurrency->current_price * 100 * ($cryptocurrency->creator_fee_percentage / 100), 2) }}</span>
                                                </div>
                                                <hr>
                                                <div class="d-flex justify-content-between font-weight-bold">
                                                    <span>Total:</span>
                                                    <span id="total-price">${{ number_format($cryptocurrency->current_price * 100 * (1 + 0.025 + ($cryptocurrency->creator_fee_percentage / 100)), 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group">
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" id="terms" required>
                                                <label class="custom-control-label" for="terms">
                                                    I understand that cryptocurrency investments involve risk and I agree to the terms and conditions
                                                </label>
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-primary btn-lg btn-block">Buy Now</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <h5 class="alert-heading"><i class="fas fa-info-circle"></i> About Creator Tokens</h5>
                        <p>When you purchase creator tokens, you're investing in the creator's success. As the creator generates revenue, token holders may receive a share of profits based on their token holdings.</p>
                        <p class="mb-0">The value of tokens may fluctuate based on market demand and the creator's performance.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amount');
        const tokenAmount = document.getElementById('token-amount');
        const subtotal = document.getElementById('subtotal');
        const platformFee = document.getElementById('platform-fee');
        const creatorFee = document.getElementById('creator-fee');
        const totalPrice = document.getElementById('total-price');
        
        const currentPrice = {{ $cryptocurrency->current_price }};
        const platformFeePercentage = 0.025;
        const creatorFeePercentage = {{ $cryptocurrency->creator_fee_percentage / 100 }};
        
        function updatePrices() {
            const amount = parseInt(amountInput.value) || 0;
            const subtotalValue = amount * currentPrice;
            const platformFeeValue = subtotalValue * platformFeePercentage;
            const creatorFeeValue = subtotalValue * creatorFeePercentage;
            const totalValue = subtotalValue + platformFeeValue + creatorFeeValue;
            
            tokenAmount.textContent = amount.toLocaleString();
            subtotal.textContent = '$' + subtotalValue.toFixed(2);
            platformFee.textContent = '$' + platformFeeValue.toFixed(2);
            creatorFee.textContent = '$' + creatorFeeValue.toFixed(2);
            totalPrice.textContent = '$' + totalValue.toFixed(2);
        }
        
        amountInput.addEventListener('input', updatePrices);
        
        // Initialize
        updatePrices();
    });
</script>
@endpush
@endsection 