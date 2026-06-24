<div class="simple-upload-wrapper">
    <button type="button" id="manual-upload-btn" class="btn btn-sm btn-primary mb-3">
        <i class="fas fa-upload mr-1"></i> {{__('Upload File')}}
    </button>
    
    <form id="manual-upload-form" style="display:none;">
        @csrf
        <input type="file" id="manual-file-input" name="file" accept="image/*,video/*,audio/*">
    </form>
</div>

<script>
// Self-executing function to avoid polluting global scope
(function() {
    // Check dependencies and init when ready
    function checkDependencies() {
        if (typeof jQuery === 'undefined') {
            console.warn('jQuery not loaded yet. Loading jQuery...');
            
            // Add jQuery if not present
            var script = document.createElement('script');
            script.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
            script.onload = function() {
                console.log('jQuery loaded successfully.');
                window.$ = window.jQuery;
                initializeUploader();
            };
            document.head.appendChild(script);
            return false;
        }
        
        return true;
    }
    
    // Initialize the uploader functionality
    function initializeUploader() {
        console.log('Initializing simple file uploader');
        
        // Define app.baseUrl if not defined
        if (typeof app === 'undefined' || !app.baseUrl) {
            window.app = window.app || {};
            app.baseUrl = '';
        }
        
        // Bind click event to button
        $(document).on('click', '#manual-upload-btn', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Manual upload button clicked');
            $('#manual-file-input').click();
        });
        
        // Handle file selection
        $(document).on('change', '#manual-file-input', function() {
            if (!this.files || !this.files[0]) return;
            
            console.log('File selected for upload');
            var file = this.files[0];
            var formData = new FormData();
            formData.append('file', file);
            formData.append('_token', $('meta[name="csrf-token"]').attr('content'));
            
            // Show loading state
            $('#manual-upload-btn').prop('disabled', true)
                .html('<i class="fas fa-spinner fa-spin mr-1"></i> {{__("Uploading...")}}');
            
            // Send the upload request
            $.ajax({
                url: app.baseUrl + '/attachment/upload/post',
                type: 'POST',
                data: formData,
                contentType: false,
                processData: false,
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    xhr.upload.addEventListener('progress', function(e) {
                        if (e.lengthComputable) {
                            var percent = Math.round((e.loaded / e.total) * 100);
                            $('#manual-upload-btn').html('<i class="fas fa-spinner fa-spin mr-1"></i> ' + percent + '%');
                        }
                    }, false);
                    return xhr;
                },
                success: function(response) {
                    console.log('Upload succeeded:', response);
                    if (response.success) {
                        // Add attachment to FileUpload if available
                        if (typeof FileUpload !== 'undefined') {
                            FileUpload.attachaments.push({
                                attachmentID: response.attachmentID, 
                                path: response.path, 
                                type: response.type,
                                thumbnail: response.thumbnail || response.path
                            });
                            
                            // Create preview element
                            createPreviewElement(response);
                        } else {
                            console.warn('FileUpload not defined, could not add to attachments list');
                            createFallbackPreview(response);
                        }
                        
                        // Success message
                        if (typeof launchToast !== 'undefined') {
                            launchToast('success', '{{__("Success")}}', '{{__("File uploaded successfully")}}');
                        } else {
                            alert('{{__("File uploaded successfully")}}');
                        }
                    } else {
                        handleUploadError('Upload failed: ' + (response.message || 'Unknown error'));
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Upload error:', xhr.responseText);
                    handleUploadError('Upload failed: ' + (xhr.responseJSON?.message || error || 'Server error'));
                },
                complete: function() {
                    // Reset button state
                    $('#manual-upload-btn').prop('disabled', false)
                        .html('<i class="fas fa-upload mr-1"></i> {{__("Upload File")}}');
                    $('#manual-file-input').val('');
                }
            });
        });
        
        // Create preview element for uploaded file
        function createPreviewElement(response) {
            var preview = $('<div class="dz-preview ml-1 mr-2 dz-processing dz-success dz-complete"></div>');
            
            // Add appropriate preview based on file type
            if (response.type === 'image') {
                preview.append('<div class="dz-image shadow"><img src="' + response.thumbnail + '" alt="Preview" /></div>');
            } else if (response.type === 'video') {
                preview.append(
                    '<div class="dz-image video-preview-item shadow">' +
                    '<video class="video-preview" controls playsinline muted loop src="' + response.path + '"></video>' +
                    '</div>'
                );
            } else if (response.type === 'audio') {
                preview.append(
                    '<div class="dz-image audio-preview-item shadow">' +
                    '<audio controls src="' + response.path + '"></audio>' +
                    '</div>'
                );
            } else {
                preview.append('<div class="dz-image shadow"><div class="file-preview">' + response.attachmentID + '</div></div>');
            }
            
            // Add remove button
            var removeBtn = $('<a class="dz-remove" href="javascript:undefined;" data-dz-remove>x</a>');
            removeBtn.on('click', function() {
                if (typeof FileUpload !== 'undefined') {
                    FileUpload.removeAttachment(response.attachmentID);
                }
                preview.remove();
            });
            preview.append(removeBtn);
            
            // Add preview to dropzone area
            $('.dropzone-previews').append(preview);
        }
        
        // Create fallback preview when FileUpload is not available
        function createFallbackPreview(response) {
            var container = $('.simple-upload-wrapper');
            var preview = $('<div class="simple-upload-preview mt-2"></div>');
            
            if (response.type === 'image') {
                preview.append('<img src="' + response.thumbnail + '" alt="Preview" style="max-width: 150px; height: auto;" />');
            } else if (response.type === 'video') {
                preview.append('<video controls width="150" src="' + response.path + '"></video>');
            }
            
            container.append(preview);
        }
        
        // Handle upload errors
        function handleUploadError(message) {
            if (typeof launchToast !== 'undefined') {
                launchToast('danger', '{{__("Error")}}', message);
            } else {
                alert(message);
            }
        }
    }
    
    // Start the initialization process
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function() {
            if (checkDependencies()) {
                initializeUploader();
            }
        });
    } else {
        if (checkDependencies()) {
            initializeUploader();
        }
    }
})();
</script> 