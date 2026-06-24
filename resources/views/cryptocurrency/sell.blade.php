@extends('layouts.generic')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h2 class="mb-0">Sell {{ $cryptocurrency->name }} ({{ $cryptocurrency->symbol }})</h2>
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
                                @if($cryptocurrency->logo && \Storage::disk('public')->exists($cryptocurrency->logo))
                                    <img src="{{ asset('storage/' . $cryptocurrency->logo) }}" alt="{{ $cryptocurrency->name }}" class="rounded-circle me-3" width="64" height="64" style="object-fit: cover;">
                                @else
                                    <div class="rounded-circle bg-danger text-white me-3 d-flex align-items-center justify-content-center" style="width: 64px; height: 64px;">
                                        <span class="h3 mb-0">{{ substr($cryptocurrency->symbol, 0, 2) }}</span>
                                    </div>
                                @endif
                                <div>
                                    <h3 class="mb-0">{{ $cryptocurrency->name }}</h3>
                                    <p class="text-muted mb-0">{{ $cryptocurrency->symbol }}</p>
                                    @if($cryptocurrency->is_verified)
                                        <span class="badge bg-primary">✓ Verified</span>
                                    @endif
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
                                            <p class="text-muted mb-1">Your Balance</p>
                                            <h4>{{ number_format($wallet->balance, 8) }}</h4>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card border-danger">
                                <div class="card-header bg-danger text-white">
                                    <h4 class="mb-0">Sale Details</h4>
                                </div>
                                <div class="card-body">
                                    <form method="POST" action="{{ route('cryptocurrency.sell', $cryptocurrency->id) }}">
                                        @csrf
                                        
                                        <div class="form-group mb-3">
                                            <label for="amount">Amount of tokens to sell</label>
                                            <input type="number" class="form-control form-control-lg @error('amount') is-invalid @enderror" id="amount" name="amount" value="{{ old('amount', number_format($wallet->balance / 4, 8, '.', '')) }}" min="0.00000001" max="{{ $wallet->balance }}" step="0.00000001" required>
                                            <div class="form-text">Enter the number of tokens you want to sell</div>
                                            @error('amount')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                        
                                        <div class="mb-3">
                                            <div class="d-flex gap-2 flex-wrap">
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setPercentage(25)">25%</button>
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setPercentage(50)">50%</button>
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setPercentage(75)">75%</button>
                                                <button type="button" class="btn btn-outline-primary btn-sm" onclick="setPercentage(100)">MAX</button>
                                            </div>
                                        </div>
                                        
                                        <div class="card bg-light mb-4">
                                            <div class="card-body">
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Price per token:</span>
                                                    <span>${{ number_format($cryptocurrency->current_price, 8) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Number of tokens:</span>
                                                    <span id="token-amount">{{ number_format($wallet->balance / 4, 8) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Gross amount:</span>
                                                    <span id="gross-amount">${{ number_format($cryptocurrency->current_price * ($wallet->balance / 4), 2) }}</span>
                                                </div>
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Platform fee ({{ $cryptocurrency->platform_fee_percentage }}%):</span>
                                                    <span id="platform-fee">-${{ number_format($cryptocurrency->current_price * ($wallet->balance / 4) * ($cryptocurrency->platform_fee_percentage / 100), 2) }}</span>
                                                </div>
                                                @if($cryptocurrency->creator_fee_percentage > 0)
                                                <div class="d-flex justify-content-between mb-2">
                                                    <span>Creator fee ({{ $cryptocurrency->creator_fee_percentage }}%):</span>
                                                    <span id="creator-fee">-${{ number_format($cryptocurrency->current_price * ($wallet->balance / 4) * ($cryptocurrency->creator_fee_percentage / 100), 2) }}</span>
                                                </div>
                                                @endif
                                                <hr>
                                                <div class="d-flex justify-content-between font-weight-bold">
                                                    <span>You'll receive:</span>
                                                    <span id="net-amount">${{ number_format($cryptocurrency->current_price * ($wallet->balance / 4) * (1 - ($cryptocurrency->platform_fee_percentage / 100) - ($cryptocurrency->creator_fee_percentage / 100)), 2) }}</span>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Improved Checkbox Section -->
                                        <div class="mb-4">
                                            <div class="card border-warning" style="background-color: #fff3cd;">
                                                <div class="card-body p-3">
                                                    <div class="form-check">
                                                        <!-- <input class="form-check-input" type="checkbox" value="" id="terms" name="terms" required> -->
                                                         <input class="form-check-input" type="checkbox" value="" id="terms" name="terms" required style="width: 20px; height: 20px;">

                                                        <label class="form-check-label" for="terms">
                                                            <div class="fw-bold text-warning-emphasis mb-1">
                                                                <i class="fas fa-exclamation-circle me-1"></i>
                                                                Agreement Required
                                                            </div>
                                                            <div class="small text-dark">
                                                                I understand that this transaction is irreversible and the tokens will be sold at the current market price. I acknowledge that cryptocurrency values are volatile and may change during the transaction process.
                                                            </div>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="d-grid">
                                            <button type="submit" class="btn btn-danger btn-lg">
                                                <i class="fas fa-coins me-2"></i>Sell Now
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Important Notice</h5>
                        <p>When you sell tokens, you're converting your holdings back to cash. The transaction is irreversible once completed.</p>
                        <p class="mb-0">The sale will be executed at the current market price. Token values may fluctuate based on market demand.</p>
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
        const grossAmount = document.getElementById('gross-amount');
        const platformFee = document.getElementById('platform-fee');
        const creatorFee = document.getElementById('creator-fee');
        const netAmount = document.getElementById('net-amount');
        
        const currentPrice = {{ $cryptocurrency->current_price }};
        const platformFeePercentage = {{ $cryptocurrency->platform_fee_percentage / 100 }};
        const creatorFeePercentage = {{ $cryptocurrency->creator_fee_percentage / 100 }};
        const maxBalance = {{ $wallet->balance }};
        
        function updatePrices() {
            const amount = parseFloat(amountInput.value) || 0;
            const grossValue = amount * currentPrice;
            const platformFeeValue = grossValue * platformFeePercentage;
            const creatorFeeValue = grossValue * creatorFeePercentage;
            const netValue = grossValue - platformFeeValue - creatorFeeValue;
            
            tokenAmount.textContent = amount.toLocaleString('en-US', {
                minimumFractionDigits: 8,
                maximumFractionDigits: 8
            });
            grossAmount.textContent = '$' + grossValue.toFixed(2);
            platformFee.textContent = '-$' + platformFeeValue.toFixed(2);
            if (creatorFee) {
                creatorFee.textContent = '-$' + creatorFeeValue.toFixed(2);
            }
            netAmount.textContent = '$' + Math.max(0, netValue).toFixed(2);
        }
        
        // Percentage buttons
        window.setPercentage = function(percentage) {
            const amount = (maxBalance * percentage) / 100;
            amountInput.value = amount.toFixed(8);
            updatePrices();
        }
        
        amountInput.addEventListener('input', updatePrices);
        
        // Initialize
        updatePrices();
    });
</script>
@endpush
@endsection