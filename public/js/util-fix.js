/**
 * File Upload Utility Script
 * This script ensures proper loading of dependencies for file uploads
 */
"use strict";

// Initialize namespace for utility functions
window.UploadFix = {
    // Track initialization status
    initialized: false,
    
    // Libraries we need to check
    libs: {
        jquery: false,
        dropzone: false,
        bootstrap: false
    },
    
    /**
     * Initialize the fix
     */
    init: function() {
        // Prevent multiple initializations
        if (this.initialized) {
            console.log('Upload fix already initialized');
            return;
        }
        
        console.log('Initializing upload fixes');
        this.initialized = true;
        
        // Check for jQuery
        this.checkJQuery();
    },
    
    /**
     * Check if jQuery is available, load if not
     */
    checkJQuery: function() {
        if (typeof jQuery !== 'undefined') {
            console.log('jQuery already loaded and configured');
            this.libs.jquery = true;
            window.$ = jQuery;
            this.checkDropzone();
        } else {
            console.log('Loading jQuery from CDN');
            var script = document.createElement('script');
            script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
            script.integrity = 'sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=';
            script.crossOrigin = 'anonymous';
            script.onload = function() {
                console.log('jQuery loaded successfully');
                window.$ = jQuery;
                UploadFix.libs.jquery = true;
                UploadFix.checkDropzone();
            };
            document.head.appendChild(script);
        }
    },
    
    /**
     * Check if Dropzone is available, load if not
     */
    checkDropzone: function() {
        if (typeof Dropzone !== 'undefined') {
            console.log('Dropzone already loaded');
            this.libs.dropzone = true;
            Dropzone.autoDiscover = false;
            this.checkBootstrap();
        } else {
            console.log('Loading Dropzone from source');
            var script = document.createElement('script');
            script.src = '/libs/dropzone/dist/dropzone.js';
            script.onload = function() {
                console.log('Dropzone loaded successfully');
                Dropzone.autoDiscover = false;
                UploadFix.libs.dropzone = true;
                UploadFix.checkBootstrap();
            };
            document.head.appendChild(script);
        }
    },
    
    /**
     * Check if Bootstrap is available (for modals)
     */
    checkBootstrap: function() {
        if (typeof $.fn !== 'undefined' && typeof $.fn.modal !== 'undefined') {
            console.log('Bootstrap already loaded');
            this.libs.bootstrap = true;
            this.initializeComponents();
        } else {
            console.log('Loading Bootstrap from source');
            var script = document.createElement('script');
            script.src = 'https://cdn.jsdelivr.net/npm/bootstrap@4.6.1/dist/js/bootstrap.bundle.min.js';
            script.onload = function() {
                console.log('Bootstrap loaded successfully');
                UploadFix.libs.bootstrap = true;
                UploadFix.initializeComponents();
            };
            document.head.appendChild(script);
        }
    },
    
    /**
     * Initialize components after all dependencies are loaded
     */
    initializeComponents: function() {
        console.log('All dependencies loaded, initializing components');
        
        // Fix file upload button event handlers
        this.fixFileUploadButtons();
        
        // Handle form submission
        this.fixFormSubmission();
        
        // Add modal fallbacks
        this.setupModalFallbacks();
    },
    
    /**
     * Fix file upload button issues
     */
    fixFileUploadButtons: function() {
        // Remove any existing handlers
        $(document).off('click', '.file-upload-button');
        
        // Add new optimized handler
        $(document).on('click', '.file-upload-button', function(e) {
            // Only handle direct clicks or from specific children
            if (e.target === this || $(e.target).hasClass('file-button-text') || $(e.target).is('svg') || $(e.target).is('path') || $(e.target).is('ion-icon')) {
                console.log('File upload button clicked');
                
                // Prevent default action and bubbling
                e.preventDefault();
                e.stopPropagation();
                
                // Check if Dropzone is working
                if (typeof FileUpload !== 'undefined' && FileUpload.myDropzone) {
                    FileUpload.myDropzone.hiddenFileInput.click();
                } else {
                    // Fallback to direct file input
                    $('#direct-file-input').click();
                }
                
                return false;
            }
        });
    },
    
    /**
     * Fix form submission issues
     */
    fixFormSubmission: function() {
        // Remove existing handlers
        $(document).off('click', '.submit-button, .save-button');
        
        // Add new handler
        $(document).on('click', '.submit-button, .save-button', function(e) {
            console.log('Save button clicked');
            
            // Find the form element
            var $form = $(this).closest('form');
            
            // If file upload in progress, prevent submission
            if (typeof FileUpload !== 'undefined' && FileUpload.isLoading) {
                e.preventDefault();
                
                if (typeof launchToast === 'function') {
                    launchToast('warning', 'Please wait', 'Please wait for file uploads to complete');
                } else {
                    alert('Please wait for file uploads to complete');
                }
                return false;
            }
            
            // Normal form submission
            if ($form.length && !$form.data('submitting')) {
                console.log('Submit form: ' + $form.attr('id'));
                $form.data('submitting', true);
                
                // Check if there's a custom handler
                var formId = $form.attr('id');
                if (formId === 'test-upload-form') {
                    // Let the form's own handlers work
                    return true;
                }
            }
        });
    },
    
    /**
     * Set up modal fallbacks when Bootstrap isn't available
     */
    setupModalFallbacks: function() {
        if (typeof $.fn === 'undefined' || typeof $.fn.modal === 'undefined') {
            console.log('Setting up modal fallbacks');
            
            // Check if PostCreate exists
            if (typeof PostCreate !== 'undefined') {
                // Ensure fallback methods exist
                if (!PostCreate.showModalFallback) {
                    PostCreate.showModalFallback = function(selector) {
                        var modal = document.querySelector(selector);
                        if (modal) {
                            modal.style.display = 'block';
                            modal.classList.add('show');
                            document.body.classList.add('modal-open');
                            
                            // Add backdrop if it doesn't exist
                            if (!document.querySelector('.modal-backdrop')) {
                                var backdrop = document.createElement('div');
                                backdrop.className = 'modal-backdrop fade show';
                                document.body.appendChild(backdrop);
                            }
                            
                            // Add close handlers
                            var closeButtons = modal.querySelectorAll('[data-dismiss="modal"], .close');
                            closeButtons.forEach(function(btn) {
                                btn.onclick = function() {
                                    PostCreate.hideModalFallback(selector);
                                };
                            });
                        }
                    };
                }
                
                if (!PostCreate.hideModalFallback) {
                    PostCreate.hideModalFallback = function(selector) {
                        var modal = document.querySelector(selector);
                        if (modal) {
                            modal.style.display = 'none';
                            modal.classList.remove('show');
                            document.body.classList.remove('modal-open');
                            
                            // Remove backdrop
                            var backdrop = document.querySelector('.modal-backdrop');
                            if (backdrop) {
                                backdrop.parentNode.removeChild(backdrop);
                            }
                        }
                    };
                }
            }
        }
    }
};

// Initialize on document load
document.addEventListener('DOMContentLoaded', function() {
    UploadFix.init();
});

// Initialize immediately if document already loaded
if (document.readyState !== 'loading') {
    UploadFix.init();
} 