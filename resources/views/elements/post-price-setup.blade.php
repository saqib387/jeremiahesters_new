<div class="modal fade" tabindex="-1" role="dialog" id="post-set-price-dialog">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Set post price')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{__('Paid posts are locked for subscribers as well.')}}</p>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <span class="input-group-text" id="amount-label">@include('elements.icon',['icon'=>'cash-outline','variant'=>'medium'])</span>
                    </div>
                    <input id="post-price" type="number" class="form-control" name="text" required  placeholder="{{__('Post price')}}" value="{{$postPrice}}">
                    <span class="invalid-feedback" role="alert">
                        <strong class="post-price-error min-error d-none">{{__('The price must be between :min and :max.',['min' => getSetting('payments.min_ppv_post_price') ?? 1, 'max' => getSetting('payments.max_ppv_post_price') ?? 500])}}</strong>
                        <strong class="post-price-error ppv-error d-none">{{__('Posts having an expire date can not be price locked.')}}</strong>
                    </span>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white price-clear-btn" onclick="PostCreate.clearPostPrice()">{{__('Clear')}}</button>
                <button type="button" class="btn btn-primary price-save-btn" onclick="PostCreate.savePostPrice()">{{__('Save')}}</button>
            </div>
        </div>
    </div>
</div>

<script>
// Fallback script for when Bootstrap modal isn't available
document.addEventListener('DOMContentLoaded', function() {
    // Wait for PostCreate to be defined
    function checkPostCreate() {
        if (typeof PostCreate === 'undefined') {
            setTimeout(checkPostCreate, 500);
            return;
        }
        
        // Only override if Bootstrap modal isn't available
        if (typeof $.fn === 'undefined' || typeof $.fn.modal === 'undefined') {
            console.log('Adding modal fallbacks for price dialog');
            
            // Override the functions if not already overridden
            if (typeof PostCreate._origShowSetPricePostDialog === 'undefined') {
                // Save original function if exists
                PostCreate._origShowSetPricePostDialog = PostCreate.showSetPricePostDialog || function(){};
                
                // Override with fallback
                PostCreate.showSetPricePostDialog = function() {
                    console.log('Using fallback price dialog');
                    var dialog = document.getElementById('post-set-price-dialog');
                    if (dialog) {
                        dialog.style.display = 'block';
                        dialog.classList.add('show');
                        document.body.classList.add('modal-open');
                    }
                };
                
                // Save original hide function
                PostCreate._origHidePriceDialog = PostCreate.hidePriceDialog || function(){};
                
                // Override hide function
                PostCreate.hidePriceDialog = function() {
                    var dialog = document.getElementById('post-set-price-dialog');
                    if (dialog) {
                        dialog.style.display = 'none';
                        dialog.classList.remove('show');
                        document.body.classList.remove('modal-open');
                    }
                };
                
                // Add click events for close buttons
                document.querySelectorAll('#post-set-price-dialog .close, #post-set-price-dialog .price-clear-btn').forEach(function(btn) {
                    btn.addEventListener('click', function() {
                        PostCreate.hidePriceDialog();
                    });
                });
                
                // Save button override
                document.querySelector('#post-set-price-dialog .price-save-btn').addEventListener('click', function() {
                    PostCreate.savePostPrice();
                    PostCreate.hidePriceDialog();
                });
            }
        }
    }
    
    // Start checking
    checkPostCreate();
});
</script>

<style>
/* Fallback modal styles when Bootstrap is not available */
#post-set-price-dialog.show {
    display: block;
    background-color: rgba(0, 0, 0, 0.5);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1050;
}

#post-set-price-dialog .modal-dialog {
    margin: 1.75rem auto;
    max-width: 500px;
}

#post-set-price-dialog .modal-content {
    background: white;
    border-radius: 0.3rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.5);
}

#post-set-price-dialog .modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
}

#post-set-price-dialog .modal-body {
    padding: 1rem;
}

#post-set-price-dialog .modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 1rem;
    border-top: 1px solid #dee2e6;
}
</style>
