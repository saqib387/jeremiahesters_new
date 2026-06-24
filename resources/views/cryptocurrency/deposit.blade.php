@extends('layouts.generic')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-4">Deposit Funds</h1>
            <p class="lead">Add funds to your wallet to purchase cryptocurrency tokens</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Deposit Details</h5>
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
                    
                    <form method="POST" action="{{ route('cryptocurrency.deposit.process') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="amount" class="form-label">Deposit Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="amount" name="amount" value="{{ old('amount', 100) }}" min="5" step="1" required>
                            </div>
                            <div class="form-text">Minimum deposit amount: $5.00</div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Payment Method</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="credit_card" {{ old('payment_method') == 'credit_card' ? 'selected' : '' }}>Credit Card</option>
                                <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="crypto" {{ old('payment_method') == 'crypto' ? 'selected' : '' }}>Cryptocurrency</option>
                            </select>
                        </div>
                        
                        <div id="payment-details" class="mb-4">
                            <!-- Dynamic content based on selected payment method -->
                            <div id="credit_card_details" class="payment-method-details">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="card_number" class="form-label">Card Number</label>
                                        <input type="text" class="form-control" id="card_number" placeholder="•••• •••• •••• ••••">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="expiry" class="form-label">Expiry</label>
                                        <input type="text" class="form-control" id="expiry" placeholder="MM/YY">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label for="cvv" class="form-label">CVV</label>
                                        <input type="text" class="form-control" id="cvv" placeholder="•••">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="card_name" class="form-label">Name on Card</label>
                                    <input type="text" class="form-control" id="card_name">
                                </div>
                            </div>
                            
                            <div id="paypal_details" class="payment-method-details d-none">
                                <div class="alert alert-info">
                                    <p class="mb-0">You will be redirected to PayPal to complete your payment after submission.</p>
                                </div>
                            </div>
                            
                            <div id="bank_transfer_details" class="payment-method-details d-none">
                                <div class="alert alert-info">
                                    <h6>Bank Transfer Instructions</h6>
                                    <p>Please transfer the exact amount to the following account:</p>
                                    <ul class="mb-0">
                                        <li>Bank Name: Example Bank</li>
                                        <li>Account Name: Token Platform LLC</li>
                                        <li>Account Number: 123456789</li>
                                        <li>Routing Number: 987654321</li>
                                        <li>Reference: Your User ID ({{ Auth::id() }})</li>
                                    </ul>
                                </div>
                            </div>
                            
                            <div id="crypto_details" class="payment-method-details d-none">
                                <div class="alert alert-info">
                                    <h6>Cryptocurrency Payment</h6>
                                    <p>Send the equivalent amount to one of the following addresses:</p>
                                    <div class="mb-2">
                                        <strong>Bitcoin (BTC):</strong>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control form-control-sm" value="3FZbgi29cpjq2GjdwV8eyHuJJnkLtktZc5" readonly>
                                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2 copy-btn">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                    <div>
                                        <strong>Ethereum (ETH):</strong>
                                        <div class="d-flex align-items-center">
                                            <input type="text" class="form-control form-control-sm" value="0x89205A3A3b2A69De6Dbf7f01ED13B2108B2c43e7" readonly>
                                            <button type="button" class="btn btn-sm btn-outline-secondary ms-2 copy-btn">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" target="_blank">Terms of Service</a> and <a href="#" target="_blank">Privacy Policy</a>
                            </label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">DEPOSIT FUNDS</button>
                            <a href="{{ route('cryptocurrency.wallet') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Summary</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Deposit Amount:</span>
                        <span id="summary-amount">$100.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Processing Fee:</span>
                        <span id="summary-fee">$0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between font-weight-bold">
                        <span>Total:</span>
                        <span id="summary-total">$100.00</span>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">Your deposited funds will be available immediately for:</p>
                    <ul class="mb-3">
                        <li>Purchasing tokens on the marketplace</li>
                        <li>Creating your own tokens</li>
                        <li>Participating in token sales</li>
                    </ul>
                    <p class="mb-0">For any issues with deposits, please <a href="#">contact support</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('payment_method');
        const paymentDetails = {
            credit_card: document.getElementById('credit_card_details'),
            paypal: document.getElementById('paypal_details'),
            bank_transfer: document.getElementById('bank_transfer_details'),
            crypto: document.getElementById('crypto_details')
        };
        const amountInput = document.getElementById('amount');
        const summaryAmount = document.getElementById('summary-amount');
        const summaryFee = document.getElementById('summary-fee');
        const summaryTotal = document.getElementById('summary-total');
        
        // Handle payment method change
        paymentMethodSelect.addEventListener('change', function() {
            const selectedMethod = this.value;
            
            // Hide all payment method details
            Object.values(paymentDetails).forEach(el => el.classList.add('d-none'));
            
            // Show selected payment method details
            paymentDetails[selectedMethod].classList.remove('d-none');
            
            // Update fees based on payment method
            updateSummary();
        });
        
        // Handle amount change
        amountInput.addEventListener('input', updateSummary);
        
        // Update summary values
        function updateSummary() {
            const amount = parseFloat(amountInput.value) || 0;
            const method = paymentMethodSelect.value;
            
            let fee = 0;
            
            // Calculate fee based on payment method
            switch (method) {
                case 'credit_card':
                    fee = amount * 0.029 + 0.30; // 2.9% + $0.30
                    break;
                case 'paypal':
                    fee = amount * 0.034 + 0.30; // 3.4% + $0.30
                    break;
                case 'bank_transfer':
                    fee = 0; // No fee for bank transfers
                    break;
                case 'crypto':
                    fee = amount * 0.01; // 1% for crypto
                    break;
            }
            
            const total = amount + fee;
            
            // Update summary display
            summaryAmount.textContent = '$' + amount.toFixed(2);
            summaryFee.textContent = '$' + fee.toFixed(2);
            summaryTotal.textContent = '$' + total.toFixed(2);
        }
        
        // Initialize summary
        updateSummary();
    });
</script>
@endpush

@push('styles')
<style>
    .form-label {
        font-weight: 500;
    }
    
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .btn-primary {
        background-color: #4A6CF7;
        border-color: #4A6CF7;
        font-weight: 500;
    }
    
    .btn-primary:hover {
        background-color: #3955cf;
        border-color: #3955cf;
    }
    
    .btn-outline-secondary {
        color: #333;
        border-color: #ced4da;
    }
    
    .btn-outline-secondary:hover {
        background-color: #f8f9fa;
        color: #333;
    }
    
    #summary-total {
        font-weight: bold;
        font-size: 1.1rem;
    }
</style>
@endpush
@endsection 