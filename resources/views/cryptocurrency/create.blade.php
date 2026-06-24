@extends('layouts.generic')

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
@endpush

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <h1 class="display-5 fw-bold text-primary"><i class="fas fa-rocket me-2"></i>Create New Token</h1>
            <p class="lead text-muted">Launch your own cryptocurrency token on the blockchain</p>
        </div>
    </div>
    
    <!-- Alerts -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <strong>Success!</strong> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>Error!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <strong>Please fix the following errors:</strong>
            <ul class="mb-0 mt-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    
    <div class="row">
        <!-- Main Form -->
        <div class="col-lg-8">
            <form action="{{ route('cryptocurrency.store') }}" method="POST" enctype="multipart/form-data" id="tokenForm">
                @csrf
                
                <!-- Basic Information -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-file-alt me-2"></i>Basic Information</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Token Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control form-control-lg" 
                                       placeholder="e.g., Bitcoin, Ethereum" value="{{ old('name') }}" required>
                                <small class="text-muted">The full name of your cryptocurrency</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Token Symbol <span class="text-danger">*</span></label>
                                <input type="text" name="symbol" id="symbol" class="form-control form-control-lg" 
                                       placeholder="e.g., BTC, ETH" value="{{ old('symbol') }}" maxlength="10" required>
                                <small class="text-muted">3-10 characters, uppercase only</small>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Description <span class="text-danger">*</span></label>
                            <textarea name="description" id="description" rows="4" class="form-control" 
                                      placeholder="Describe your token's purpose and unique features..." required>{{ old('description') }}</textarea>
                            <div class="d-flex justify-between mt-1">
                                <small class="text-muted">Minimum 50 characters</small>
                                <small id="charCount" class="text-muted">0/2000</small>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Token Logo <span class="text-muted">(Optional)</span></label>
                                <div class="logo-upload-area" id="logoUploadArea">
                                    <input type="file" name="logo" id="logoInput" class="d-none" accept="image/*">
                                    <div class="logo-upload-content">
                                        <div class="logo-preview-wrapper">
                                            <div id="logoPreviewPlaceholder" class="logo-preview-placeholder">
                                                <i class="fas fa-coins"></i>
                                            </div>
                                            <img id="logoPreview" src="" alt="Logo Preview" class="logo-preview d-none">
                                            <div class="logo-overlay d-none" id="logoOverlay">
                                                <i class="fas fa-camera"></i>
                                            </div>
                                        </div>
                                        <div class="logo-upload-info">
                                            <button type="button" class="btn btn-sm btn-outline-primary logo-upload-btn" onclick="document.getElementById('logoInput').click()">
                                                <i class="fas fa-upload me-1"></i>Upload Logo
                                            </button>
                                            <small class="d-block mt-2 text-muted">
                                                <i class="fas fa-info-circle me-1"></i>Max 2MB, JPG/PNG/GIF/SVG
                                            </small>
                                            <small class="d-block mt-1 text-muted logo-file-name" id="logoFileName"></small>
                                        </div>
                                    </div>
                                    <div class="logo-drop-zone">
                                        <i class="fas fa-cloud-upload-alt"></i>
                                        <p class="mb-0">Drag & drop your logo here</p>
                                        <small>or click to browse</small>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Website URL</label>
                                <input type="url" name="website" class="form-control" 
                                       placeholder="https://yourtoken.com" value="{{ old('website') }}">
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Whitepaper URL</label>
                            <input type="url" name="whitepaper" class="form-control" 
                                   placeholder="https://yourtoken.com/whitepaper.pdf" value="{{ old('whitepaper') }}">
                        </div>
                    </div>
                </div>

                <!-- Token Configuration -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-cog me-2"></i>Token Configuration</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Blockchain Network <span class="text-danger">*</span></label>
                                <select name="blockchain_network" class="form-select form-select-lg" required>
                                    <option value="ethereum">Ethereum (ETH) - Most Popular</option>
                                    <option value="binance">Binance Smart Chain (BSC) - Low Fees</option>
                                    <option value="polygon">Polygon (MATIC) - Fast & Cheap</option>
                                    <option value="solana">Solana (SOL) - High Performance</option>
                                    <option value="avalanche">Avalanche (AVAX) - Fast Finality</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Token Type <span class="text-danger">*</span></label>
                                <select name="token_type" class="form-select form-select-lg" required>
                                    <option value="utility">Utility Token - Platform usage</option>
                                    <option value="security">Security Token - Investment</option>
                                    <option value="governance">Governance Token - Voting rights</option>
                                    <option value="payment">Payment Token - Currency</option>
                                    <option value="nft">NFT Collection - Collectibles</option>
                                    <option value="defi">DeFi Token - Finance</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Supply & Economics -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i>Supply & Economics</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Initial Price (USD) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="initial_price" id="initialPrice" class="form-control form-control-lg" 
                                           value="0.001" step="0.00000001" min="0.00000001" required>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Total Supply <span class="text-danger">*</span></label>
                                <input type="number" name="total_supply" id="totalSupply" class="form-control form-control-lg" 
                                       value="1000000" min="1" required>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Creator Allocation</label>
                                <input type="number" name="creator_allocation" id="creatorAllocation" class="form-control form-control-lg" 
                                       value="100000" min="0">
                                <small class="text-muted">Tokens you'll receive initially</small>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label fw-semibold">Market Cap</label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="text" id="marketCap" class="form-control form-control-lg bg-light" readonly>
                                </div>
                                <small class="text-muted">Total Supply × Initial Price</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fee Structure -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-percent me-2"></i>Fee Structure</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Creator Fee (%)</label>
                                <div class="input-group">
                                    <input type="number" name="creator_fee_percentage" class="form-control" 
                                           value="5" step="0.01" min="0" max="20">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Platform Fee (%)</label>
                                <div class="input-group">
                                    <input type="text" class="form-control bg-light" value="2.50" readonly>
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">Liquidity Pool (%)</label>
                                <div class="input-group">
                                    <input type="number" name="liquidity_pool_percentage" class="form-control" 
                                           value="20" step="0.01" min="0" max="100">
                                    <span class="input-group-text">%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Token Features -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-tools me-2"></i>Token Features</h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <div class="feature-option p-3 border rounded">
                                    <div class="form-check">
                                        <input type="checkbox" name="enable_burning" value="1" id="burning" class="form-check-input" style="width: 20px; height: 20px;">
                                        <label class="form-check-label fw-semibold" for="burning">
                                            <i class="fas fa-fire me-2"></i>Token Burning
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-1">Allow permanent token destruction</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="feature-option p-3 border rounded">
                                    <div class="form-check">
                                        <input type="checkbox" name="enable_minting" value="1" id="minting" class="form-check-input" style="width: 20px; height: 20px;">
                                        <label class="form-check-label fw-semibold" for="minting">
                                            <i class="fas fa-hammer me-2"></i>Token Minting
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-1">Allow new token creation</small>
                                </div>
                            </div>
                            <div class="col-md-4 mb-3">
                                <div class="feature-option p-3 border rounded">
                                    <div class="form-check">
                                        <input type="checkbox" name="transferable" value="1" id="transfers" class="form-check-input" style="width: 20px; height: 20px;" checked>
                                        <label class="form-check-label fw-semibold" for="transfers">
                                            <i class="fas fa-exchange-alt me-2"></i>Allow Transfers
                                        </label>
                                    </div>
                                    <small class="text-muted d-block mt-1">Enable token transfers</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Terms & Submit -->
                <div class="card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="form-check mb-4">
                            <input type="checkbox" id="terms" class="form-check-input" style="width: 20px; height: 20px;" required>
                            <label class="form-check-label" for="terms">
                                I agree to the <a href="#" class="text-decoration-none">Terms of Service</a> and understand the risks involved in token creation.
                            </label>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg py-3">
                                <span id="submitSpinner" class="spinner-border spinner-border-sm me-2 d-none"></span>
                                <i class="fas fa-rocket me-2"></i>CREATE TOKEN
                            </button>
                            <a href="{{ route('cryptocurrency.index') }}" class="btn btn-outline-secondary"><i class="fas fa-times me-2"></i>Cancel</a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            @if(isset($userTokenCount) && isset($maxTokensPerUser))
            <!-- Token Limit -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-bullseye me-2"></i>Token Limit</h6>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span>Created:</span>
                        <strong>{{ $userTokenCount }}/{{ $maxTokensPerUser }}</strong>
                    </div>
                    <div class="progress mb-2">
                        <div class="progress-bar bg-success" style="width: {{ ($userTokenCount / $maxTokensPerUser) * 100 }}%"></div>
                    </div>
                    <small class="text-muted">{{ $maxTokensPerUser - $userTokenCount }} tokens remaining</small>
                </div>
            </div>
            @endif
            
            <!-- Summary -->
            <div class="card mb-4 border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-chart-bar me-2"></i>Summary</h6>
                </div>
                <div class="card-body">
                    <div class="row g-2 text-center">
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <div class="h6 mb-0" id="summaryMarketCap">$0.00</div>
                                <small class="text-muted">Market Cap</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <div class="h6 mb-0" id="summaryAllocation">0</div>
                                <small class="text-muted">Your Tokens</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <div class="h6 mb-0" id="summaryAvailable">0</div>
                                <small class="text-muted">For Sale</small>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-2 bg-light rounded">
                                <div class="h6 mb-0" id="summaryTotal">0</div>
                                <small class="text-muted">Total Supply</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Info -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-light">
                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Information</h6>
                </div>
                <div class="card-body">
                    <p class="small mb-2">After creating your token:</p>
                    <ul class="small mb-3 ps-3">
                        <li><i class="fas fa-store me-1"></i>List on marketplace</li>
                        <li><i class="fas fa-users me-1"></i>Build community</li>
                        <li><i class="fas fa-chart-line me-1"></i>Track performance</li>
                        <li><i class="fas fa-wallet me-1"></i>Manage economics</li>
                    </ul>
                    <div class="alert alert-info small p-2 mb-0">
                        <strong><i class="fas fa-lightbulb me-1"></i>Tip:</strong> Clear description attracts more users!
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Logo Upload Area - Modern Design */
.logo-upload-area {
    border: 2px dashed #dee2e6;
    border-radius: 16px;
    padding: 1.5rem;
    background: linear-gradient(135deg, #f8f9fa 0%, #ffffff 100%);
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.logo-upload-area:hover {
    border-color: #830866;
    background: linear-gradient(135deg, rgba(131, 8, 102, 0.05) 0%, rgba(255, 255, 255, 1) 100%);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(131, 8, 102, 0.1);
}

.logo-upload-area.drag-over {
    border-color: #830866;
    background: linear-gradient(135deg, rgba(131, 8, 102, 0.1) 0%, rgba(255, 255, 255, 1) 100%);
    transform: scale(1.02);
}

.logo-upload-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.logo-preview-wrapper {
    width: 100px;
    height: 100px;
    border-radius: 20px;
    overflow: hidden;
    border: 3px solid #e5e7eb;
    background: linear-gradient(135deg, #f8f9fa 0%, #f3f4f6 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    position: relative;
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.logo-upload-area:hover .logo-preview-wrapper {
    border-color: #830866;
    box-shadow: 0 4px 16px rgba(131, 8, 102, 0.2);
    transform: scale(1.05);
}

.logo-preview-placeholder {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 40px;
    color: #830866;
    transition: all 0.3s ease;
}

.logo-upload-area:hover .logo-preview-placeholder {
    transform: scale(1.1) rotate(5deg);
}

.logo-preview {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: all 0.3s ease;
    position: absolute;
    top: 0;
    left: 0;
}

.logo-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(131, 8, 102, 0.8);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 24px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.logo-preview-wrapper:hover .logo-overlay {
    opacity: 1;
}

.logo-upload-info {
    flex: 1;
}

.logo-upload-btn {
    border-radius: 10px;
    padding: 0.5rem 1.25rem;
    font-weight: 600;
    transition: all 0.3s ease;
}

.logo-upload-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(131, 8, 102, 0.2);
}

.logo-file-name {
    color: #830866 !important;
    font-weight: 600;
}

.logo-drop-zone {
    display: none;
    text-align: center;
    padding: 2rem;
    color: #6b7280;
}

.logo-drop-zone i {
    font-size: 48px;
    color: #830866;
    margin-bottom: 1rem;
    display: block;
    animation: bounce 2s infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

.logo-upload-area.drag-over .logo-upload-content {
    display: none;
}

.logo-upload-area.drag-over .logo-drop-zone {
    display: block;
}

.logo-upload-area.has-logo {
    border-color: #830866;
    background: linear-gradient(135deg, rgba(131, 8, 102, 0.05) 0%, rgba(255, 255, 255, 1) 100%);
}

.logo-overlay {
    cursor: pointer;
}

.logo-overlay i {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); }
    50% { transform: scale(1.1); }
}

/* Feature Options - Enhanced */
.feature-option {
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.feature-option::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(131, 8, 102, 0.1), transparent);
    transition: left 0.5s ease;
}

.feature-option:hover::before {
    left: 100%;
}

.feature-option:hover {
    border-color: #830866 !important;
    background: linear-gradient(135deg, rgba(131, 8, 102, 0.05) 0%, rgba(255, 255, 255, 1) 100%);
    transform: translateY(-4px);
    box-shadow: 0 6px 20px rgba(131, 8, 102, 0.15);
}

.feature-option.selected {
    border-color: #830866 !important;
    background: linear-gradient(135deg, rgba(131, 8, 102, 0.1) 0%, rgba(255, 255, 255, 1) 100%);
    box-shadow: 0 4px 16px rgba(131, 8, 102, 0.2);
}

.feature-option .form-check-input {
    width: 22px;
    height: 22px;
    cursor: pointer;
    border: 2px solid #dee2e6;
    transition: all 0.3s ease;
}

.feature-option:hover .form-check-input {
    border-color: #830866;
}

.feature-option .form-check-input:checked {
    background-color: #830866;
    border-color: #830866;
}

.feature-option .form-check-label {
    font-weight: 600;
    color: #1f2937;
    transition: color 0.3s ease;
}

.feature-option:hover .form-check-label {
    color: #830866;
}

.feature-option i {
    transition: transform 0.3s ease;
}

.feature-option:hover i {
    transform: scale(1.2);
}

/* Card Enhancements */
.card {
    border-radius: 16px;
    transition: all 0.3s ease;
    border: 1px solid #e5e7eb;
}

.card:hover {
    box-shadow: 0 8px 24px rgba(131, 8, 102, 0.1);
}

.card-header {
    border-radius: 16px 16px 0 0 !important;
}

/* Button Enhancements */
.btn-primary {
    background: linear-gradient(135deg, #830866 0%, #a10a7f 100%);
    border: none;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 12px rgba(131, 8, 102, 0.3);
}

.btn-primary:hover {
    background: linear-gradient(135deg, #6a0652 0%, #830866 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(131, 8, 102, 0.4);
}

.btn-primary:active {
    transform: translateY(0);
}

.btn-outline-primary {
    border-color: #830866;
    color: #830866;
    transition: all 0.3s ease;
}

.btn-outline-primary:hover {
    background: #830866;
    border-color: #830866;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(131, 8, 102, 0.2);
}

/* Form Controls - Enhanced */
.form-control:focus, .form-select:focus {
    border-color: #830866;
    box-shadow: 0 0 0 4px rgba(131, 8, 102, 0.1);
    transform: translateY(-1px);
}

.form-control, .form-select {
    transition: all 0.3s ease;
}

.form-control:hover:not(:disabled):not(:focus), .form-select:hover:not(:disabled):not(:focus) {
    border-color: #adb5bd;
}

/* Progress Bar */
.progress {
    height: 10px;
    border-radius: 8px;
    background-color: #e9ecef;
    overflow: hidden;
    box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.1);
}

.progress-bar {
    background: linear-gradient(90deg, #830866 0%, #a10a7f 100%);
    transition: width 0.6s ease;
}

/* Input Group */
.input-group-text {
    min-width: 45px;
    justify-content: center;
    background: linear-gradient(135deg, #f8f9fa 0%, #f3f4f6 100%);
    border-color: #dee2e6;
    transition: all 0.3s ease;
}

.input-group:focus-within .input-group-text {
    background: linear-gradient(135deg, rgba(131, 8, 102, 0.1) 0%, rgba(255, 255, 255, 1) 100%);
    border-color: #830866;
    color: #830866;
}

/* Summary Cards */
.summary-card .bg-light {
    transition: all 0.3s ease;
}

.summary-card .bg-light:hover {
    background: linear-gradient(135deg, rgba(131, 8, 102, 0.05) 0%, rgba(255, 255, 255, 1) 100%) !important;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(131, 8, 102, 0.1);
}

/* Input Validation Feedback */
.form-control.is-valid {
    border-color: #10b981;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2310b981' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    padding-right: calc(1.5em + 0.75rem);
}

.form-control.is-invalid {
    border-color: #ef4444;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23ef4444'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath d='m5.8 3.6 1.4 1.4m0 1.4-1.4 1.4'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
    padding-right: calc(1.5em + 0.75rem);
}

/* Label Enhancements */
.form-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #374151;
}

.form-label .text-danger {
    color: #ef4444;
    font-size: 0.875em;
}

/* Character Count Enhancement */
#charCount {
    font-weight: 600;
    transition: color 0.3s ease;
}

#charCount.text-success {
    color: #10b981;
}

#charCount.text-warning {
    color: #f59e0b;
}

#charCount.text-danger {
    color: #ef4444;
}

/* Smooth Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: fadeIn 0.5s ease-out;
}

.card:nth-child(1) { animation-delay: 0.1s; }
.card:nth-child(2) { animation-delay: 0.2s; }
.card:nth-child(3) { animation-delay: 0.3s; }
.card:nth-child(4) { animation-delay: 0.4s; }
.card:nth-child(5) { animation-delay: 0.5s; }

/* Loading State */
.btn-primary:disabled {
    opacity: 0.7;
    cursor: not-allowed;
    transform: none !important;
}

/* Tooltip Enhancement */
[data-bs-toggle="tooltip"] {
    cursor: help;
}

/* Summary Numbers Animation */
.summary-card .h6 {
    transition: all 0.3s ease;
    font-weight: 800;
}

.summary-card .h6:hover {
    transform: scale(1.1);
    color: #830866;
}

/* Responsive */
@media (max-width: 768px) {
    .logo-upload-content {
        flex-direction: column;
        text-align: center;
    }
    
    .logo-preview-wrapper {
        width: 80px;
        height: 80px;
    }
    
    .logo-preview-placeholder {
        font-size: 32px;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Elements
    const logoInput = document.getElementById('logoInput');
    const logoPreview = document.getElementById('logoPreview');
    const symbolInput = document.getElementById('symbol');
    const descriptionInput = document.getElementById('description');
    const charCount = document.getElementById('charCount');
    const initialPrice = document.getElementById('initialPrice');
    const totalSupply = document.getElementById('totalSupply');
    const creatorAllocation = document.getElementById('creatorAllocation');
    const marketCap = document.getElementById('marketCap');
    const form = document.getElementById('tokenForm');
    
    // Summary elements
    const summaryMarketCap = document.getElementById('summaryMarketCap');
    const summaryAllocation = document.getElementById('summaryAllocation');
    const summaryAvailable = document.getElementById('summaryAvailable');
    const summaryTotal = document.getElementById('summaryTotal');
    
    // Logo upload area elements
    const logoUploadArea = document.getElementById('logoUploadArea');
    const logoPreviewPlaceholder = document.getElementById('logoPreviewPlaceholder');
    const logoOverlay = document.getElementById('logoOverlay');
    const logoFileName = document.getElementById('logoFileName');
    
    // Logo preview functionality
    function handleLogoPreview(file) {
        if (file) {
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size must be less than 2MB');
                logoInput.value = '';
                return;
            }
            
            // Validate file type
            const validTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/svg+xml'];
            if (!validTypes.includes(file.type)) {
                alert('Please upload a valid image file (JPG, PNG, GIF, or SVG)');
                logoInput.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                logoPreview.src = e.target.result;
                logoPreview.classList.remove('d-none');
                logoOverlay.classList.remove('d-none');
                if (logoPreviewPlaceholder) {
                    logoPreviewPlaceholder.style.display = 'none';
                }
                if (logoFileName) {
                    logoFileName.textContent = file.name;
                    logoFileName.style.display = 'block';
                }
                logoUploadArea.classList.add('has-logo');
            };
            reader.readAsDataURL(file);
        } else {
            logoPreview.classList.add('d-none');
            logoOverlay.classList.add('d-none');
            if (logoPreviewPlaceholder) {
                logoPreviewPlaceholder.style.display = 'flex';
            }
            if (logoFileName) {
                logoFileName.style.display = 'none';
            }
            logoUploadArea.classList.remove('has-logo');
        }
    }
    
    // File input change
    logoInput.addEventListener('change', function() {
        handleLogoPreview(this.files[0]);
    });
    
    // Click to upload
    logoUploadArea.addEventListener('click', function(e) {
        if (!e.target.closest('.logo-overlay') && !e.target.closest('.logo-upload-btn')) {
            logoInput.click();
        }
    });
    
    // Drag and drop functionality
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        logoUploadArea.addEventListener(eventName, preventDefaults, false);
    });
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    ['dragenter', 'dragover'].forEach(eventName => {
        logoUploadArea.addEventListener(eventName, function() {
            logoUploadArea.classList.add('drag-over');
        }, false);
    });
    
    ['dragleave', 'drop'].forEach(eventName => {
        logoUploadArea.addEventListener(eventName, function() {
            logoUploadArea.classList.remove('drag-over');
        }, false);
    });
    
    logoUploadArea.addEventListener('drop', function(e) {
        const dt = e.dataTransfer;
        const files = dt.files;
        if (files.length > 0) {
            logoInput.files = files;
            handleLogoPreview(files[0]);
        }
    }, false);
    
    // Remove logo functionality
    logoOverlay.addEventListener('click', function(e) {
        e.stopPropagation();
        if (confirm('Remove logo?')) {
            logoInput.value = '';
            handleLogoPreview(null);
        }
    });
    
    // Symbol uppercase
    symbolInput.addEventListener('input', function() {
        this.value = this.value.toUpperCase().replace(/[^A-Z0-9]/g, '');
    });
    
    // Character count with validation
    descriptionInput.addEventListener('input', function() {
        const length = this.value.length;
        charCount.textContent = length + '/2000';
        if (length < 50) {
            charCount.className = 'text-danger';
            this.classList.remove('is-valid');
            this.classList.add('is-invalid');
        } else if (length > 1800) {
            charCount.className = 'text-warning';
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        } else {
            charCount.className = 'text-success';
            this.classList.remove('is-invalid');
            this.classList.add('is-valid');
        }
    });
    
    // Real-time validation for required fields
    const requiredInputs = document.querySelectorAll('input[required], textarea[required], select[required]');
    requiredInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value.trim() === '') {
                this.classList.add('is-invalid');
                this.classList.remove('is-valid');
            } else {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
        
        input.addEventListener('input', function() {
            if (this.value.trim() !== '') {
                this.classList.remove('is-invalid');
                this.classList.add('is-valid');
            }
        });
    });
    
    // Calculate values
    function updateCalculations() {
        const price = parseFloat(initialPrice.value) || 0;
        const supply = parseFloat(totalSupply.value) || 0;
        const allocation = parseFloat(creatorAllocation.value) || 0;
        const available = supply - allocation;
        const cap = price * supply;
        
        marketCap.value = cap.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        summaryMarketCap.textContent = '$' + cap.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
        summaryAllocation.textContent = allocation.toLocaleString();
        summaryAvailable.textContent = available.toLocaleString();
        summaryTotal.textContent = supply.toLocaleString();
    }
    
    initialPrice.addEventListener('input', updateCalculations);
    totalSupply.addEventListener('input', updateCalculations);
    creatorAllocation.addEventListener('input', updateCalculations);
    
    // Feature options
    document.querySelectorAll('.feature-option').forEach(option => {
        const checkbox = option.querySelector('input[type="checkbox"]');
        
        option.addEventListener('click', function(e) {
            if (e.target.type !== 'checkbox') {
                checkbox.checked = !checkbox.checked;
            }
            option.classList.toggle('selected', checkbox.checked);
        });
        
        checkbox.addEventListener('change', function() {
            option.classList.toggle('selected', this.checked);
        });
        
        // Initialize
        option.classList.toggle('selected', checkbox.checked);
    });
    
    // Form submission with enhanced feedback
    form.addEventListener('submit', function(e) {
        // Validate description length
        const descLength = descriptionInput.value.length;
        if (descLength < 50) {
            e.preventDefault();
            alert('Description must be at least 50 characters long.');
            descriptionInput.focus();
            return false;
        }
        
        const submitBtn = form.querySelector('button[type="submit"]');
        const spinner = document.getElementById('submitSpinner');
        submitBtn.disabled = true;
        spinner.classList.remove('d-none');
        
        // Animate button
        submitBtn.style.transform = 'scale(0.98)';
        setTimeout(() => {
            submitBtn.style.transform = '';
        }, 100);
        
        submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span><i class="fas fa-rocket me-2"></i>Creating Token...';
        
        // Add pulsing effect
        submitBtn.style.animation = 'pulse 1.5s infinite';
    });
    
    // Add smooth scroll to errors
    if (document.querySelector('.alert-danger')) {
        setTimeout(() => {
            document.querySelector('.alert-danger').scrollIntoView({ 
                behavior: 'smooth', 
                block: 'center' 
            });
        }, 300);
    }
    
    // Add tooltips if Bootstrap tooltips are available
    if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Initialize
    updateCalculations();
    descriptionInput.dispatchEvent(new Event('input'));
});
</script>
@endsection