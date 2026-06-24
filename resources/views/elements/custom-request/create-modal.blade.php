<div class="modal fade" tabindex="-1" role="dialog" id="createCustomRequestModal">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content custom-request-modal">
            <div class="modal-header custom-request-header">
                <div class="header-content">
                    <div class="header-icon">
                        <i class="fas fa-gift"></i>
                    </div>
                    <div>
                        <h5 class="modal-title">{{ __('Create Custom Request') }}</h5>
                        <p class="modal-subtitle">{{ __('Request custom content from your favorite creators') }}</p>
                    </div>
                </div>
                <button type="button" class="close" data-dismiss="modal" aria-label="{{__('Close')}}">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="createCustomRequestForm">
                <div class="modal-body custom-request-body">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    
                    <!-- Creator Selection Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="fas fa-user-circle"></i>
                            <label for="request_creator_id" class="section-label">{{ __('Select Creator') }} <span class="text-danger">*</span></label>
                        </div>
                        <div class="form-group creator-search-group">
                            <div class="input-wrapper">
                                <i class="fas fa-search input-icon"></i>
                                <input type="text" 
                                       class="form-control form-control-lg" 
                                       id="request_creator_username" 
                                       name="creator_username" 
                                       placeholder="{{ __('Search for a creator...') }}" 
                                       required 
                                       autocomplete="off">
                                <input type="hidden" id="request_creator_id" name="creator_id" required>
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> {{ __('Type a username and select from the results below') }}
                            </small>
                            
                            <!-- Selected Creator Indicator -->
                            <div id="creator_selected_indicator" class="creator-selected-badge" style="display: none;">
                                <div class="selected-creator-content">
                                    <i class="fas fa-check-circle"></i>
                                    <div>
                                        <strong>{{ __('Selected Creator') }}:</strong>
                                        <span id="selected_creator_name"></span>
                                    </div>
                                    <button type="button" class="btn-remove-creator" onclick="clearCreatorSelection()">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            
                            <!-- Search Results -->
                            <div id="creator_search_results" class="creator-search-results" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- Request Type Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="fas fa-tag"></i>
                            <label for="request_type" class="section-label">{{ __('Request Type') }} <span class="text-danger">*</span></label>
                        </div>
                        <div class="request-type-cards">
                            <div class="type-card" data-type="private">
                                <input type="radio" name="type" id="type_private" value="private" required>
                                <label for="type_private" class="type-card-label">
                                    <div class="type-icon">
                                        <i class="fas fa-lock"></i>
                                    </div>
                                    <div class="type-content">
                                        <strong>{{ __('Private Request') }}</strong>
                                        <small>{{ __('Sent via private message') }}</small>
                                    </div>
                                </label>
                            </div>
                            <div class="type-card" data-type="public">
                                <input type="radio" name="type" id="type_public" value="public" required>
                                <label for="type_public" class="type-card-label">
                                    <div class="type-icon">
                                        <i class="fas fa-globe"></i>
                                    </div>
                                    <div class="type-content">
                                        <strong>{{ __('Public Request') }}</strong>
                                        <small>{{ __('Visible on creator profile') }}</small>
                                    </div>
                                </label>
                            </div>
                            <div class="type-card" data-type="marketplace">
                                <input type="radio" name="type" id="type_marketplace" value="marketplace" required>
                                <label for="type_marketplace" class="type-card-label">
                                    <div class="type-icon">
                                        <i class="fas fa-store"></i>
                                    </div>
                                    <div class="type-content">
                                        <strong>{{ __('Marketplace Request') }}</strong>
                                        <small>{{ __('Crowdfunded with contributions') }}</small>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Request Details Section -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="fas fa-edit"></i>
                            <label class="section-label">{{ __('Request Details') }}</label>
                        </div>
                        
                        <div class="form-group">
                            <label for="request_title" class="form-label">
                                <i class="fas fa-heading"></i> {{ __('Title') }} <span class="text-danger">*</span>
                            </label>
                            <input type="text" 
                                   class="form-control form-control-lg" 
                                   id="request_title" 
                                   name="title" 
                                   placeholder="{{ __('e.g., Shave My Head, Sing Happy Birthday, etc.') }}" 
                                   required 
                                   maxlength="255">
                            <small class="char-count"><span id="title_count">0</span>/255</small>
                        </div>

                        <div class="form-group">
                            <label for="request_description" class="form-label">
                                <i class="fas fa-align-left"></i> {{ __('Description') }} <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" 
                                      id="request_description" 
                                      name="description" 
                                      rows="5" 
                                      placeholder="{{ __('Describe your custom request in detail. Be specific about what you want...') }}" 
                                      required></textarea>
                            <small class="char-count"><span id="description_count">0</span> {{ __('characters') }}</small>
                        </div>
                    </div>

                    <!-- Pricing Section (Dynamic) -->
                    <div id="price_field" class="form-section" style="display: none;">
                        <div class="section-header">
                            <i class="fas fa-dollar-sign"></i>
                            <label for="request_price" class="section-label">{{ __('Pricing') }} <span class="text-danger">*</span></label>
                        </div>
                        <div class="form-group">
                            <div class="price-input-wrapper">
                                <span class="currency-symbol">$</span>
                                <input type="number" 
                                       class="form-control form-control-lg price-input" 
                                       id="request_price" 
                                       name="price" 
                                       step="0.01" 
                                       min="1" 
                                       placeholder="0.00">
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> {{ __('Fixed price for this request (minimum $1)') }}
                            </small>
                        </div>
                        
                        <!-- Upfront Payment Info -->
                        <div class="upfront-payment-info" id="upfront_payment_info" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-credit-card"></i>
                                <strong>{{ __('Upfront Payment Required') }}:</strong>
                                <span id="upfront_payment_amount">$0.00</span>
                                <small class="d-block mt-1">{{ __('You will need to pay this amount to create the request') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Goal Amount Section (Marketplace) -->
                    <div id="goal_amount_field" class="form-section" style="display: none;">
                        <div class="section-header">
                            <i class="fas fa-bullseye"></i>
                            <label for="request_goal_amount" class="section-label">{{ __('Funding Goal') }} <span class="text-danger">*</span></label>
                        </div>
                        <div class="form-group">
                            <div class="price-input-wrapper">
                                <span class="currency-symbol">$</span>
                                <input type="number" 
                                       class="form-control form-control-lg price-input" 
                                       id="request_goal_amount" 
                                       name="goal_amount" 
                                       step="0.01" 
                                       min="1" 
                                       placeholder="1000.00">
                            </div>
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> {{ __('Total amount needed (e.g., $1000 to shave my head)') }}
                            </small>
                        </div>
                        
                        <!-- Upfront Payment Info for Marketplace -->
                        <div class="upfront-payment-info" id="upfront_payment_marketplace" style="display: none;">
                            <div class="alert alert-info">
                                <i class="fas fa-credit-card"></i>
                                <strong>{{ __('Upfront Payment Required') }}:</strong>
                                <span id="upfront_payment_marketplace_amount">$0.00</span>
                                <small class="d-block mt-1">{{ __('10% of goal amount or $1 minimum (whichever is higher)') }}</small>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Options -->
                    <div class="form-section">
                        <div class="section-header">
                            <i class="fas fa-cog"></i>
                            <label class="section-label">{{ __('Additional Options') }}</label>
                        </div>
                        
                        <div class="form-group">
                            <label for="request_deadline" class="form-label">
                                <i class="fas fa-calendar-alt"></i> {{ __('Deadline') }} <span class="text-muted">({{ __('Optional') }})</span>
                            </label>
                            <input type="date" 
                                   class="form-control form-control-lg" 
                                   id="request_deadline" 
                                   name="deadline"
                                   min="{{ date('Y-m-d') }}">
                            <small class="form-text text-muted">
                                <i class="fas fa-info-circle"></i> {{ __('When should this request be completed?') }}
                            </small>
                        </div>

                        <div id="message_id_field" class="form-group" style="display: none;">
                            <label for="request_message_id" class="form-label">
                                <i class="fas fa-envelope"></i> {{ __('Message ID') }} <span class="text-muted">({{ __('Optional') }})</span>
                            </label>
                            <input type="number" 
                                   class="form-control form-control-lg" 
                                   id="request_message_id" 
                                   name="message_id" 
                                   placeholder="{{ __('Message ID if sent via private message') }}">
                        </div>
                    </div>
                </div>
                <div class="modal-footer custom-request-footer">
                    <button type="button" class="btn btn-outline-secondary btn-lg" data-dismiss="modal">
                        <i class="fas fa-times"></i> {{ __('Cancel') }}
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg btn-create">
                        <i class="fas fa-plus-circle"></i> {{ __('Create Request') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Custom Request Modal Styles */
.custom-request-modal {
    border-radius: 20px;
    border: none;
    box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    overflow: hidden;
}

.custom-request-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border-bottom: none;
    padding: 2rem;
}

.custom-request-header .header-content {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.custom-request-header .header-icon {
    width: 50px;
    height: 50px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
}

.custom-request-header .modal-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin: 0;
    color: white;
}

.custom-request-header .modal-subtitle {
    font-size: 0.9rem;
    margin: 0.25rem 0 0 0;
    opacity: 0.9;
    color: white;
}

.custom-request-header .close {
    color: white;
    opacity: 0.9;
    font-size: 1.5rem;
    text-shadow: none;
}

.custom-request-header .close:hover {
    opacity: 1;
}

.custom-request-body {
    padding: 2rem;
    max-height: 70vh;
    overflow-y: auto;
}

.custom-request-body::-webkit-scrollbar {
    width: 8px;
}

.custom-request-body::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.custom-request-body::-webkit-scrollbar-thumb {
    background: #667eea;
    border-radius: 10px;
}

.custom-request-body::-webkit-scrollbar-thumb:hover {
    background: #764ba2;
}

/* Form Sections */
.form-section {
    margin-bottom: 2rem;
    padding-bottom: 1.5rem;
    border-bottom: 1px solid #e9ecef;
}

.form-section:last-child {
    border-bottom: none;
}

.section-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.section-header i {
    color: #667eea;
    font-size: 1.2rem;
}

.section-label {
    font-weight: 600;
    font-size: 1.1rem;
    color: #2d3748;
    margin: 0;
}

/* Creator Search */
.creator-search-group {
    position: relative;
}

.input-wrapper {
    position: relative;
}

.input-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: #a0aec0;
    z-index: 1;
}

.input-wrapper .form-control {
    padding-left: 3rem;
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    transition: all 0.3s ease;
}

.input-wrapper .form-control:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.creator-selected-badge {
    margin-top: 1rem;
    padding: 1rem;
    background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    border-radius: 12px;
    color: white;
    animation: slideDown 0.3s ease;
}

.selected-creator-content {
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.selected-creator-content i.fa-check-circle {
    font-size: 1.5rem;
}

.btn-remove-creator {
    margin-left: auto;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 28px;
    height: 28px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.2s;
}

.btn-remove-creator:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: scale(1.1);
}

/* Search Results */
.creator-search-results {
    margin-top: 0.5rem;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    animation: slideDown 0.3s ease;
}

.creator-search-results .list-group {
    border-radius: 12px;
    border: none;
}

.creator-search-results .list-group-item {
    border: none;
    border-bottom: 1px solid #e2e8f0;
    padding: 1rem 1.25rem;
    transition: all 0.2s;
    cursor: pointer;
}

.creator-search-results .list-group-item:last-child {
    border-bottom: none;
}

.creator-search-results .list-group-item:hover {
    background: #f7fafc;
    transform: translateX(5px);
}

.creator-search-results .list-group-item strong {
    color: #2d3748;
    display: block;
    margin-bottom: 0.25rem;
}

.creator-search-results .list-group-item small {
    color: #718096;
}

/* Request Type Cards */
.request-type-cards {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 1rem;
}

.type-card {
    position: relative;
}

.type-card input[type="radio"] {
    position: absolute;
    opacity: 0;
    pointer-events: none;
}

.type-card-label {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 1.5rem 1rem;
    border: 2px solid #e2e8f0;
    border-radius: 12px;
    cursor: pointer;
    transition: all 0.3s ease;
    background: white;
    text-align: center;
}

.type-card-label:hover {
    border-color: #667eea;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.15);
}

.type-card input[type="radio"]:checked + .type-card-label {
    border-color: #667eea;
    background: linear-gradient(135deg, rgba(102, 126, 234, 0.1) 0%, rgba(118, 75, 162, 0.1) 100%);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.2);
}

.type-icon {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.5rem;
    margin-bottom: 0.75rem;
    transition: all 0.3s ease;
}

.type-card input[type="radio"]:checked + .type-card-label .type-icon {
    transform: scale(1.1);
    box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
}

.type-content strong {
    display: block;
    color: #2d3748;
    font-size: 0.95rem;
    margin-bottom: 0.25rem;
}

.type-content small {
    color: #718096;
    font-size: 0.8rem;
}

/* Form Controls */
.form-control-lg {
    border-radius: 12px;
    border: 2px solid #e2e8f0;
    padding: 0.75rem 1rem;
    transition: all 0.3s ease;
}

.form-control-lg:focus {
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.form-label {
    font-weight: 600;
    color: #2d3748;
    margin-bottom: 0.5rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.form-label i {
    color: #667eea;
}

.char-count {
    display: block;
    text-align: right;
    color: #a0aec0;
    font-size: 0.85rem;
    margin-top: 0.25rem;
}

/* Price Input */
.price-input-wrapper {
    position: relative;
    display: flex;
    align-items: center;
}

.currency-symbol {
    position: absolute;
    left: 1rem;
    font-size: 1.25rem;
    font-weight: 600;
    color: #667eea;
    z-index: 1;
}

.price-input {
    padding-left: 2.5rem !important;
}

/* Footer */
.custom-request-footer {
    background: #f7fafc;
    border-top: 1px solid #e2e8f0;
    padding: 1.5rem 2rem;
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
}

.btn-lg {
    padding: 0.75rem 2rem;
    border-radius: 12px;
    font-weight: 600;
    transition: all 0.3s ease;
}

.btn-primary.btn-lg {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
}

.btn-primary.btn-lg:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.btn-outline-secondary.btn-lg {
    border: 2px solid #e2e8f0;
    color: #718096;
}

.btn-outline-secondary.btn-lg:hover {
    background: #f7fafc;
    border-color: #cbd5e0;
}

.btn-lg i {
    margin-right: 0.5rem;
}

/* Animations */
@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Upfront Payment Info */
.upfront-payment-info {
    margin-top: 1rem;
    animation: slideDown 0.3s ease;
}

.upfront-payment-info .alert {
    border-radius: 12px;
    border-left: 4px solid #4299e1;
    background: linear-gradient(135deg, rgba(66, 153, 225, 0.1) 0%, rgba(59, 130, 246, 0.1) 100%);
}

.upfront-payment-info .alert strong {
    color: #2d3748;
}

.upfront-payment-info .alert span {
    font-size: 1.25rem;
    font-weight: 700;
    color: #4299e1;
}

/* Responsive */
@media (max-width: 768px) {
    .request-type-cards {
        grid-template-columns: 1fr;
    }
    
    .custom-request-body {
        padding: 1.5rem;
    }
    
    .custom-request-header {
        padding: 1.5rem;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const requestType = document.getElementById('request_type');
    const priceField = document.getElementById('price_field');
    const goalAmountField = document.getElementById('goal_amount_field');
    const messageIdField = document.getElementById('message_id_field');
    const priceInput = document.getElementById('request_price');
    const goalAmountInput = document.getElementById('request_goal_amount');
    const titleInput = document.getElementById('request_title');
    const descriptionInput = document.getElementById('request_description');
    const titleCount = document.getElementById('title_count');
    const descriptionCount = document.getElementById('description_count');

    // Character counters
    if (titleInput && titleCount) {
        titleInput.addEventListener('input', function() {
            titleCount.textContent = this.value.length;
        });
    }

    if (descriptionInput && descriptionCount) {
        descriptionInput.addEventListener('input', function() {
            descriptionCount.textContent = this.value.length;
        });
    }

    // Request type selection with radio buttons
    const typeCards = document.querySelectorAll('.type-card');
    typeCards.forEach(card => {
        const radio = card.querySelector('input[type="radio"]');
        const label = card.querySelector('.type-card-label');
        
        label.addEventListener('click', function() {
            // Uncheck all radios
            document.querySelectorAll('input[name="type"]').forEach(r => r.checked = false);
            // Check this one
            radio.checked = true;
            // Trigger change event
            requestType.value = radio.value;
            requestType.dispatchEvent(new Event('change'));
        });
    });

    // Handle request type change
    function handleTypeChange() {
        const type = (requestType ? requestType.value : null) || (document.querySelector('input[name="type"]:checked')?.value);
        
        // Hide all fields first
        priceField.style.display = 'none';
        goalAmountField.style.display = 'none';
        messageIdField.style.display = 'none';
        priceInput?.removeAttribute('required');
        goalAmountInput?.removeAttribute('required');
        
        // Hide upfront payment info
        document.getElementById('upfront_payment_info')?.style.setProperty('display', 'none');
        document.getElementById('upfront_payment_marketplace')?.style.setProperty('display', 'none');

        // Show relevant fields based on type
        if (type === 'private' || type === 'public') {
            priceField.style.display = 'block';
            priceInput?.setAttribute('required', 'required');
            priceInput?.setAttribute('min', '1');
            if (type === 'private') {
                messageIdField.style.display = 'block';
            }
            
            // Show upfront payment info when price is entered
            if (priceInput) {
                priceInput.addEventListener('input', updateUpfrontPayment);
            }
        } else if (type === 'marketplace') {
            goalAmountField.style.display = 'block';
            goalAmountInput?.setAttribute('required', 'required');
            goalAmountInput?.setAttribute('min', '1');
            
            // Show upfront payment info when goal is entered
            if (goalAmountInput) {
                goalAmountInput.addEventListener('input', updateUpfrontPaymentMarketplace);
            }
        }
    }
    
    // Update upfront payment display for private/public
    function updateUpfrontPayment() {
        const price = parseFloat(priceInput.value) || 0;
        const upfrontPayment = Math.max(1.00, price);
        const upfrontInfo = document.getElementById('upfront_payment_info');
        const upfrontAmount = document.getElementById('upfront_payment_amount');
        
        if (upfrontInfo && upfrontAmount && price > 0) {
            upfrontAmount.textContent = '$' + upfrontPayment.toFixed(2);
            upfrontInfo.style.display = 'block';
        } else if (upfrontInfo && price === 0) {
            upfrontInfo.style.display = 'none';
        }
    }
    
    // Update upfront payment display for marketplace
    function updateUpfrontPaymentMarketplace() {
        const goal = parseFloat(goalAmountInput.value) || 0;
        const upfrontPayment = Math.max(1.00, goal * 0.10); // 10% or $1
        const upfrontInfo = document.getElementById('upfront_payment_marketplace');
        const upfrontAmount = document.getElementById('upfront_payment_marketplace_amount');
        
        if (upfrontInfo && upfrontAmount && goal > 0) {
            upfrontAmount.textContent = '$' + upfrontPayment.toFixed(2);
            upfrontInfo.style.display = 'block';
        } else if (upfrontInfo && goal === 0) {
            upfrontInfo.style.display = 'none';
        }
    }

    if (requestType) {
        requestType.addEventListener('change', handleTypeChange);
    }

    // Also listen to radio button changes
    document.querySelectorAll('input[name="type"]').forEach(radio => {
        radio.addEventListener('change', function() {
            if (requestType) {
                requestType.value = this.value;
            }
            handleTypeChange();
        });
    });

    // Creator username search
    const creatorUsernameInput = document.getElementById('request_creator_username');
    const creatorIdInput = document.getElementById('request_creator_id');
    const creatorResults = document.getElementById('creator_search_results');
    let searchTimeout;

    if (creatorUsernameInput) {
        creatorUsernameInput.addEventListener('input', function() {
            const username = this.value.trim();
            
            clearTimeout(searchTimeout);
            
            if (username.length < 2) {
                creatorResults.style.display = 'none';
                creatorIdInput.value = '';
                return;
            }

            searchTimeout = setTimeout(function() {
                fetch('{{ route("search.users") }}?query=' + encodeURIComponent(username) + '&encode_html=false', {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(responseData => {
                    creatorResults.innerHTML = '';
                    
                    // Handle different response structures from search API
                    let users = [];
                    if (responseData.success && responseData.data) {
                        if (responseData.data.data && Array.isArray(responseData.data.data)) {
                            users = responseData.data.data;
                        } else if (responseData.data.users && Array.isArray(responseData.data.users)) {
                            users = responseData.data.users;
                        } else if (Array.isArray(responseData.data)) {
                            users = responseData.data;
                        }
                    } else if (responseData.users && Array.isArray(responseData.users)) {
                        users = responseData.users;
                    } else if (Array.isArray(responseData)) {
                        users = responseData;
                    }
                    
                    if (users && users.length > 0) {
                        creatorResults.style.display = 'block';
                        const list = document.createElement('div');
                        list.className = 'list-group';
                        list.style.maxHeight = '300px';
                        list.style.overflowY = 'auto';
                        
                        users.slice(0, 5).forEach(user => {
                            let userId, userName, userUsername, userAvatar;
                            
                            if (user.id) {
                                userId = user.id;
                                userName = user.name || 'Unknown';
                                userUsername = user.username || '';
                                userAvatar = user.avatar || '';
                            } else if (user.user_id) {
                                userId = user.user_id;
                                userName = user.user_name || 'Unknown';
                                userUsername = user.user_username || '';
                                userAvatar = user.avatar || '';
                            } else {
                                return;
                            }
                            
                            const item = document.createElement('a');
                            item.href = '#';
                            item.className = 'list-group-item list-group-item-action';
                            item.style.cursor = 'pointer';
                            item.innerHTML = `
                                <div style="display: flex; align-items: center; gap: 0.75rem;">
                                    ${userAvatar ? `<img src="${userAvatar}" style="width: 40px; height: 40px; border-radius: 50%; object-fit: cover;">` : '<div style="width: 40px; height: 40px; border-radius: 50%; background: #667eea; display: flex; align-items: center; justify-content: center; color: white;"><i class="fas fa-user"></i></div>'}
                                    <div>
                                        <strong>${userName}</strong>
                                        <small class="text-muted d-block">@${userUsername}</small>
                                    </div>
                                </div>
                            `;
                            item.addEventListener('click', function(e) {
                                e.preventDefault();
                                creatorUsernameInput.value = userUsername;
                                creatorIdInput.value = userId;
                                creatorResults.style.display = 'none';
                                
                                // Show selected indicator
                                const indicator = document.getElementById('creator_selected_indicator');
                                const selectedName = document.getElementById('selected_creator_name');
                                if (indicator && selectedName) {
                                    selectedName.textContent = userName + ' (@' + userUsername + ')';
                                    indicator.style.display = 'block';
                                }
                            });
                            list.appendChild(item);
                        });
                        
                        if (list.children.length > 0) {
                            creatorResults.appendChild(list);
                        } else {
                            creatorResults.style.display = 'none';
                        }
                    } else {
                        creatorResults.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error searching creators:', error);
                    creatorResults.style.display = 'none';
                });
            }, 500);
        });
    }
});

// Clear creator selection
function clearCreatorSelection() {
    document.getElementById('request_creator_username').value = '';
    document.getElementById('request_creator_id').value = '';
    document.getElementById('creator_selected_indicator').style.display = 'none';
    document.getElementById('creator_search_results').style.display = 'none';
}
</script>
