<div class="modal fade" tabindex="-1" role="dialog" id="confirm-post-save">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">{{__('Notice!')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <p>{{__('Some attachments are still being uploaded.')}} {{__('Are you sure you want to continue?')}}</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">{{__('Cancel')}}</button>
                <button type="button" class="btn btn-primary confirm-post-save">{{__('Continue')}}</button>
            </div>
        </div>
    </div>
</div>

<script>
// Fallback for Bootstrap modal in case it's not available
document.addEventListener('DOMContentLoaded', function() {
    // Check if Bootstrap modal function exists
    if (typeof $.fn === 'undefined' || typeof $.fn.modal === 'undefined') {
        console.log('Bootstrap modal not available for post save dialog, using fallback');
        
        // Add event handlers
        document.querySelector('#confirm-post-save .close').addEventListener('click', function() {
            hideConfirmDialog();
        });
        
        document.querySelector('#confirm-post-save .btn-white').addEventListener('click', function() {
            hideConfirmDialog();
        });
        
        // Override the modal function
        if (typeof $ !== 'undefined') {
            $.fn.modal = function(action) {
                var modalEl = this[0];
                if (!modalEl) return;
                
                if (action === 'show') {
                    modalEl.style.display = 'block';
                    modalEl.classList.add('show');
                    document.body.classList.add('modal-open');
                } else if (action === 'hide') {
                    hideConfirmDialog();
                }
                
                return this;
            };
        }
        
        // Global function to hide dialog
        window.hideConfirmDialog = function() {
            var modal = document.querySelector('#confirm-post-save');
            if (modal) {
                modal.style.display = 'none';
                modal.classList.remove('show');
                document.body.classList.remove('modal-open');
            }
        };
    }
});
</script>

<style>
/* Fallback modal styles when Bootstrap is not available */
#confirm-post-save.show {
    display: block;
    background-color: rgba(0, 0, 0, 0.5);
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    z-index: 1050;
}

#confirm-post-save .modal-dialog {
    margin: 1.75rem auto;
    max-width: 500px;
}

#confirm-post-save .modal-content {
    background: white;
    border-radius: 0.3rem;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.5);
}

#confirm-post-save .modal-header {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    padding: 1rem;
    border-bottom: 1px solid #dee2e6;
}

#confirm-post-save .modal-body {
    padding: 1rem;
}

#confirm-post-save .modal-footer {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 1rem;
    border-top: 1px solid #dee2e6;
}
</style>
