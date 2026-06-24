@extends('layouts.generic')

@section('page_title', __('Cryptocurrency UI Test'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card crypto-card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ __('Cryptocurrency UI Test') }}</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle mr-2"></i>
                        {{ __('This page tests cryptocurrency UI components and JavaScript functionality.') }}
                    </div>
                    
                    <!-- Crypto Card Example -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card wallet-card">
                                <div class="card-body">
                                    <div class="wallet-details mb-3">
                                        <div class="crypto-symbol">J</div>
                                        <div>
                                            <h5 class="mb-0">JustCoin</h5>
                                            <span class="badge bg-secondary">JCOIN</span>
                                        </div>
                                    </div>
                                    
                                    <div class="balance-display">
                                        <div class="balance-label">Balance</div>
                                        <div class="balance-value">100,000</div>
                                    </div>
                                    
                                    <p>
                                        <strong>Wallet Address:</strong>
                                        <span id="wallet-address" class="text-muted">0x123456789abcdef123456789abcdef123456789a</span>
                                        <button id="copy-wallet-address" class="btn btn-sm btn-outline-primary ms-2">
                                            <i class="fas fa-copy"></i>
                                        </button>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="card crypto-card">
                                <div class="card-body">
                                    <h5 class="card-title">Transaction Filters</h5>
                                    <div class="btn-group mb-3" role="group">
                                        <button type="button" class="btn btn-outline-primary transaction-filter active" data-filter="all">All</button>
                                        <button type="button" class="btn btn-outline-primary transaction-filter" data-filter="buy">Buy</button>
                                        <button type="button" class="btn btn-outline-primary transaction-filter" data-filter="sell">Sell</button>
                                        <button type="button" class="btn btn-outline-primary transaction-filter" data-filter="transfer">Transfer</button>
                                    </div>
                                    
                                    <div class="table-responsive">
                                        <table class="table transaction-table">
                                            <thead>
                                                <tr>
                                                    <th>Type</th>
                                                    <th>Amount</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr class="transaction-row" data-type="buy">
                                                    <td><span class="transaction-buy">Buy</span></td>
                                                    <td>1,000</td>
                                                    <td><span class="badge badge-completed">Completed</span></td>
                                                </tr>
                                                <tr class="transaction-row" data-type="sell">
                                                    <td><span class="transaction-sell">Sell</span></td>
                                                    <td>500</td>
                                                    <td><span class="badge badge-completed">Completed</span></td>
                                                </tr>
                                                <tr class="transaction-row" data-type="transfer">
                                                    <td><span class="transaction-transfer">Transfer</span></td>
                                                    <td>200</td>
                                                    <td><span class="badge badge-pending">Pending</span></td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Price Chart Example -->
                    <div class="row">
                        <div class="col-12">
                            <div class="card crypto-card">
                                <div class="card-body">
                                    <h5 class="card-title">Price History</h5>
                                    <div class="price-chart-container" style="height: 300px;">
                                        <canvas id="price-chart" 
                                            data-labels="['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun']"
                                            data-values="[0.01, 0.012, 0.015, 0.014, 0.016, 0.015]"
                                        ></canvas>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Test JavaScript Functionality -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        console.log('Testing cryptocurrency.js functionality');
        
        // Check if Cryptocurrency object exists
        if (typeof Cryptocurrency === 'undefined') {
            console.error('Cryptocurrency object not loaded!');
            
            // Add error message to the page
            const alertBox = document.createElement('div');
            alertBox.className = 'alert alert-danger mt-3';
            alertBox.innerHTML = '<strong>Error:</strong> cryptocurrency.js not loaded correctly.';
            document.querySelector('.container').prepend(alertBox);
        } else {
            console.log('Cryptocurrency object loaded successfully!');
            
            // Add success message to the page
            const alertBox = document.createElement('div');
            alertBox.className = 'alert alert-success mt-3';
            alertBox.innerHTML = '<strong>Success:</strong> cryptocurrency.js loaded correctly.';
            document.querySelector('.container').prepend(alertBox);
        }
    });
</script>
@endsection 