/**
 * Enhanced Attachment Upload Script
 * This script provides improved functionality for file uploads in the application
 */

(function() {
    // Wait for document ready
    document.addEventListener('DOMContentLoaded', function() {
        // Check jQuery availability
        if (typeof $ === 'undefined') {
            console.error('jQuery not available for attachment uploads');
            var script = document.createElement('script');
            script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
            script.onload = initUploads;
            document.head.appendChild(script);
        } else {
            initUploads();
        }
    });
    
    function initUploads() {
        // Additional direct upload functionality
        $('.file-upload-button').on('click', function(e) {
            // Check if Dropzone is working properly
            if (typeof Dropzone === 'undefined' || 
                typeof FileUpload === 'undefined' || 
                FileUpload.myDropzone === null) {
                
                // Use fallback direct upload method
                e.preventDefault();
                e.stopPropagation();
                $('#direct-file-input').click();
                return false;
            }
        });
        
        // Simple upload button functionality
        $('#manual-upload-btn').on('click', function(e) {
            e.preventDefault();
            $('#manual-file-input').click();
        });
        
        // Ensure file inputs work properly
        if ($('#manual-file-input').length) {
            $('#manual-file-input').on('click', function(e) {
                e.stopPropagation();
            });
        }
        
        if ($('#direct-file-input').length) {
            $('#direct-file-input').on('click', function(e) {
                e.stopPropagation();
            });
        }
        
        // Monitor Dropzone initialization for errors
        let dropzoneMonitorInterval = setInterval(function() {
            if (typeof Dropzone !== 'undefined') {
                if (Dropzone.instances.length === 0 && $('.dropzone').length > 0) {
                    console.warn('Dropzone not initialized properly');
                    // Add a class to indicate issues with Dropzone
                    $('.dropzone').addClass('dropzone-fallback');
                } else {
                    clearInterval(dropzoneMonitorInterval);
                }
            }
        }, 2000);
        
        // Clear interval after 10 seconds to prevent memory leaks
        setTimeout(function() {
            clearInterval(dropzoneMonitorInterval);
        }, 10000);
    }
})(); 