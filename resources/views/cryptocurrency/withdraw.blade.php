@extends('layouts.generic')

@section('content')
<div class="container py-4">
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-4">Withdraw Funds</h1>
            <p class="lead">Cash out your cryptocurrency earnings to your preferred payment method</p>
        </div>
    </div>
    
    <div class="row">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Withdrawal Details</h5>
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
                    
                    <form method="POST" action="{{ route('cryptocurrency.withdraw.process') }}">
                        @csrf
                        
                        <div class="mb-3">
                            <label for="amount" class="form-label">Withdrawal Amount</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control" id="amount" name="amount" value="{{ old('amount', 20) }}" min="20" max="{{ $totalBalance }}" step="1" required>
                            </div>
                            <div class="form-text">
                                Minimum withdrawal: $20.00 | Available balance: ${{ number_format($totalBalance, 2) }}
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="payment_method" class="form-label">Withdrawal Method</label>
                            <select class="form-select" id="payment_method" name="payment_method" required>
                                <option value="paypal" {{ old('payment_method') == 'paypal' ? 'selected' : '' }}>PayPal</option>
                                <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                                <option value="crypto" {{ old('payment_method') == 'crypto' ? 'selected' : '' }}>Cryptocurrency</option>
                            </select>
                        </div>
                        
                        <div id="withdrawal-details" class="mb-4">
                            <!-- Dynamic content based on selected withdrawal method -->
                            <div id="paypal_details" class="withdrawal-method-details">
                                <div class="mb-3">
                                    <label for="paypal_email" class="form-label">PayPal Email</label>
                                    <input type="email" class="form-control" id="paypal_email" name="withdrawal_address" value="{{ old('withdrawal_address') }}" required>
                                </div>
                            </div>
                            
                            <div id="bank_transfer_details" class="withdrawal-method-details d-none">
                                <div class="mb-3">
                                    <label for="bank_name" class="form-label">Bank Name</label>
                                    <input type="text" class="form-control" id="bank_name">
                                </div>
                                <div class="mb-3">
                                    <label for="account_name" class="form-label">Account Name</label>
                                    <input type="text" class="form-control" id="account_name">
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="account_number" class="form-label">Account Number</label>
                                        <input type="text" class="form-control" id="account_number">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="routing_number" class="form-label">Routing Number</label>
                                        <input type="text" class="form-control" id="routing_number">
                                    </div>
                                </div>
                                <input type="hidden" name="withdrawal_address" id="bank_details_json" value="{{ old('withdrawal_address') }}">
                            </div>
                            
                            <div id="crypto_details" class="withdrawal-method-details d-none">
                                <div class="mb-3">
                                    <label for="crypto_type" class="form-label">Cryptocurrency</label>
                                    <select class="form-select" id="crypto_type">
                                        <option value="btc">Bitcoin (BTC)</option>
                                        <option value="eth">Ethereum (ETH)</option>
                                        <option value="usdt">Tether (USDT)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="wallet_address" class="form-label">Wallet Address</label>
                                    <input type="text" class="form-control" id="wallet_address" name="withdrawal_address" value="{{ old('withdrawal_address') }}">
                                </div>
                                <div class="form-text text-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Double check your wallet address. Transactions cannot be reversed.
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="terms" required>
                            <label class="form-check-label" for="terms">
                                I confirm this withdrawal information is correct
                            </label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">REQUEST WITHDRAWAL</button>
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
                        <span>Withdrawal Amount:</span>
                        <span id="summary-amount">$20.00</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Processing Fee:</span>
                        <span id="summary-fee">$0.00</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between font-weight-bold">
                        <span>You'll Receive:</span>
                        <span id="summary-total">$20.00</span>
                    </div>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Important Information</h5>
                </div>
                <div class="card-body">
                    <p class="mb-3">Please note the following withdrawal conditions:</p>
                    <ul class="mb-3">
                        <li>Withdrawals are typically processed within 1-3 business days</li>
                        <li>Minimum withdrawal amount is $20.00</li>
                        <li>Some withdrawal methods may have additional verification requirements</li>
                        <li>Bank transfers may take 3-5 business days to appear in your account</li>
                    </ul>
                    <p class="mb-0">For any issues with withdrawals, please <a href="#">contact support</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const paymentMethodSelect = document.getElementById('payment_method');
        const withdrawalDetails = {
            paypal: document.getElementById('paypal_details'),
            bank_transfer: document.getElementById('bank_transfer_details'),
            crypto: document.getElementById('crypto_details')
        };
        const amountInput = document.getElementById('amount');
        const summaryAmount = document.getElementById('summary-amount');
        const summaryFee = document.getElementById('summary-fee');
        const summaryTotal = document.getElementById('summary-total');
        
        // Bank transfer fields
        const bankName = document.getElementById('bank_name');
        const accountName = document.getElementById('account_name');
        const accountNumber = document.getElementById('account_number');
        const routingNumber = document.getElementById('routing_number');
        const bankDetailsJson = document.getElementById('bank_details_json');
        
        // Handle payment method change
        paymentMethodSelect.addEventListener('change', function() {
            const selectedMethod = this.value;
            
            // Hide all payment method details
            Object.values(withdrawalDetails).forEach(el => el.classList.add('d-none'));
            
            // Show selected payment method details
            withdrawalDetails[selectedMethod].classList.remove('d-none');
            
            // Update fees based on payment method
            updateSummary();
        });
        
        // Handle amount change
        amountInput.addEventListener('input', updateSummary);
        
        // Update bank details JSON
        function updateBankDetails() {
            const details = {
                bank_name: bankName.value,
                account_name: accountName.value,
                account_number: accountNumber.value,
                routing_number: routingNumber.value
            };
            bankDetailsJson.value = JSON.stringify(details);
        }
        
        // Add event listeners for bank fields
        bankName.addEventListener('input', updateBankDetails);
        accountName.addEventListener('input', updateBankDetails);
        accountNumber.addEventListener('input', updateBankDetails);
        routingNumber.addEventListener('input', updateBankDetails);
        
        // Update summary values
        function updateSummary() {
            const amount = parseFloat(amountInput.value) || 0;
            const method = paymentMethodSelect.value;
            
            let fee = 0;
            
            // Calculate fee based on withdrawal method
            switch (method) {
                case 'paypal':
                    fee = amount * 0.025 + 0.25; // 2.5% + $0.25
                    break;
                case 'bank_transfer':
                    fee = amount < 1000 ? 3 : 0; // $3 fee for amounts under $1000
                    break;
                case 'crypto':
                    fee = amount * 0.015; // 1.5% for crypto
                    break;
            }
            
            const total = amount - fee;
            
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