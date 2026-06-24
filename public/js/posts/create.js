/*
* Post create page
 */
"use strict";
/* global PostCreate, FileUpload, mediaSettings, isAllowedToPost, AiSuggestions, app, trans */

// jQuery availability check
(function() {
    // Check if jQuery is available
    if (typeof jQuery === 'undefined') {
        console.error('jQuery not loaded in create.js. Attempting to load it.');
        
        // Create a script element to load jQuery
        var script = document.createElement('script');
        script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
        script.integrity = 'sha256-/xUj+3OJU5yExlq6GSYGSHk7tPXikynS7ogEvDej/m4=';
        script.crossOrigin = 'anonymous';
        script.onload = function() {
            console.log('jQuery loaded successfully in create.js');
            initializeCreatePage();
        };
        document.head.appendChild(script);
    } else {
        // jQuery is already available, initialize the page
        $(document).ready(function() {
            initializeCreatePage();
        });
    }
})();

// Main initialization function
function initializeCreatePage() {
    // Wait for document ready
    if (document.readyState !== 'loading') {
        setupCreatePage();
    } else {
        document.addEventListener('DOMContentLoaded', setupCreatePage);
    }
}

function setupCreatePage() {
    console.log('Setting up post create page');

    // Initing button save
    $('.post-create-button').on('click', function () {
        PostCreate.save('create');
    });

    $('.draft-clear-button').on('click', function () {
        PostCreate.clearDraft();
    });
    
    // Populating draft data, if available
    const draftData = PostCreate.populateDraftData();
    PostCreate.initPostDraft(draftData);
    
    if(typeof isAllowedToPost !== 'undefined' && isAllowedToPost){
        // Ensure jQuery and Dropzone are both available
        if (typeof $ === 'undefined' || typeof Dropzone === 'undefined') {
            console.error('Missing dependencies for file upload');
        } else {
            // Set default config
            Dropzone.autoDiscover = false;
            
            // Initialize file uploader
            if (typeof FileUpload !== 'undefined') {
                FileUpload.initDropZone('#dropzone-uploader', '/attachment/upload/post', false);
            }
        }
    
        // Fix for file upload buttons
        setupFileUploadButtons();
        
        // Fix for price button
        setupPriceButton();
    }
    
    if (typeof app !== 'undefined' && app.open_ai_enabled) {
        if (typeof AiSuggestions !== 'undefined') {
            AiSuggestions.initAISuggestions('#dropzone-uploader', 'post');
        }
    }
}

/**
 * Setup proper file upload button handlers
 */
function setupFileUploadButtons() {
    // File upload button - dedicated handler
    $(document).on('click', '.file-upload-button', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        // Ensure we only trigger the file dialog, not text editor
        if ($(e.target).hasClass('file-upload-button') || $(e.target).parent().hasClass('file-upload-button')) {
            if (typeof FileUpload !== 'undefined' && FileUpload.myDropzone) {
                FileUpload.myDropzone.hiddenFileInput.click();
            } else {
                // Fallback to direct file input
                $('#direct-file-input').click();
            }
        }
    });
    
    // Manual upload button
    $(document).on('click', '#manual-upload-btn', function(e) {
        e.preventDefault();
        $('#manual-file-input').click();
    });
    
    // Prevent bubbling from child elements inside .file-upload-button
    $(document).on('click', '.file-upload-button *', function(e) {
        e.stopPropagation();
    });
}

/**
 * Setup price button handlers
 */
function setupPriceButton() {
    // Price button handler
    $(document).on('click', '.post-price-button', function(e) {
        e.preventDefault();
        
        // Only proceed if user is verified or verification not enforced
        if(!$(this).hasClass('disabled')) {
            PostCreate.showSetPricePostDialog();
        }
    });
    
    // Price input handler - submit on Enter
    $("#post-price").on('keypress', function(e) {
        if(e.which === 13) {
            PostCreate.savePostPrice();
        }
    });
}

// Saving draft data before unload
window.addEventListener('beforeunload', function (event) {
    // Forcing a dialog when a file is being uploaded/video transcoded
    if(FileUpload.isTranscodingVideo === true || FileUpload.isLoading === true){
        event.returnValue = trans('Are you sure you want to leave?');
    }
    if(!PostCreate.isSavingRedirect){
        PostCreate.saveDraftData();
    }
});
