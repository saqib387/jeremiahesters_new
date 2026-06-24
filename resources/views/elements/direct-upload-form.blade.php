<!-- Simple direct file upload form -->
<div id="direct-upload-form" class="d-none">
    <form id="simple-upload-form" action="{{ route('attachment.upload', ['type' => 'post']) }}" method="post" enctype="multipart/form-data">
        @csrf
        <input type="file" name="file" id="direct-file-input" class="d-none">
    </form>
</div>

<!-- Include utility fix script -->
<script src="{{ asset('js/util-fix.js') }}"></script>

<script>
    // Global fix for jQuery and Dropzone loading issues
    (function() {
        // Check if jQuery is available
        if (typeof jQuery === 'undefined') {
            console.warn('jQuery not detected! Loading from CDN...');
            var jqScript = document.createElement('script');
            jqScript.src = 'https://code.jquery.com/jquery-3.6.0.min.js';
            jqScript.onload = function() {
                console.log('jQuery loaded successfully!');
                window.$ = jQuery;
                loadDropzone();
            };
            document.head.appendChild(jqScript);
        } else {
            loadDropzone();
        }

        function loadDropzone() {
            if (typeof Dropzone === 'undefined') {
                console.warn('Dropzone not detected! Loading from source...');
                var dzScript = document.createElement('script');
                dzScript.src = '{{ asset('libs/dropzone/dist/dropzone.js') }}';
                dzScript.onload = function() {
                    console.log('Dropzone loaded successfully!');
                    Dropzone.autoDiscover = false;
                    initializeUpload();
                };
                document.head.appendChild(dzScript);
            } else {
                Dropzone.autoDiscover = false;
                initializeUpload();
            }
        }

        function initializeUpload() {
            // Wait for document to be ready
            if (document.readyState !== 'loading') {
                setupUploads();
            } else {
                document.addEventListener('DOMContentLoaded', setupUploads);
            }
        }

        function setupUploads() {
            console.log('Setting up direct file upload handlers...');
            
            // Function to trigger manual file uploads - ensure we only bind this once
            $(document).off('click', '.file-upload-button').on('click', '.file-upload-button', function(e) {
                // Make sure we only handle the click once
                if (e.target === this || $(e.target).hasClass('file-button-text') || $(e.target).is('svg') || $(e.target).is('path')) {
                    console.log('File upload button clicked');
                    
                    // Check if Dropzone is working
                    if (typeof FileUpload !== 'undefined' && FileUpload.myDropzone) {
                        e.preventDefault();
                        e.stopPropagation();
                        FileUpload.myDropzone.hiddenFileInput.click();
                    } else {
                        // Fallback to direct file input
                        e.preventDefault();
                        e.stopPropagation();
                        $('#direct-file-input').click();
                    }
                    
                    return false;
                }
            });

            // Make sure we only bind this event once
            $('#direct-file-input').off('change').on('change', function() {
                if (this.files && this.files[0]) {
                    var selectedFile = this.files[0];
                    var localPreviewSource = window.URL.createObjectURL(selectedFile);
                    var pendingPreview = null;
                    var normalizePreviewUrl = function(url) {
                        if (!url) return '';
                        if (url.indexOf('blob:') === 0 || url.indexOf('data:') === 0 || /^https?:\/\//i.test(url)) return url;
                        if (url.charAt(0) === '/') return url;
                        return '{{ rtrim(url('/'), '/') }}/' + url.replace(/^\//, '');
                    };
                    var imageFallbackDataUrl = function() {
                        return 'data:image/svg+xml;charset=UTF-8,' + encodeURIComponent('<svg xmlns="http://www.w3.org/2000/svg" width="480" height="320" viewBox="0 0 480 320"><rect width="480" height="320" fill="#eef2f7"/><path d="M92 238l83-92 57 63 42-46 114 75H92z" fill="#cbd5e1"/><circle cx="350" cy="92" r="34" fill="#94a3b8"/><text x="240" y="282" text-anchor="middle" font-family="Arial, sans-serif" font-size="20" fill="#64748b">Preview unavailable</text></svg>');
                    };
                    var applyOneShotImageFallback = function(img, fallbackSource) {
                        if (!img) return;
                        if (fallbackSource) img.dataset.fallbackSrc = fallbackSource;
                        img.onerror = function() {
                            var nextSource = this.dataset.fallbackSrc;
                            this.onerror = null;
                            if (nextSource && this.src !== nextSource) {
                                this.src = nextSource;
                                this.dataset.fallbackSrc = '';
                                this.onerror = function() {
                                    this.onerror = null;
                                    this.src = imageFallbackDataUrl();
                                };
                                return;
                            }
                            this.src = imageFallbackDataUrl();
                        };
                    };

                    pendingPreview = $('<div class="dz-preview ml-1 mr-2 dz-processing"></div>');
                    $('.dropzone-previews').append(pendingPreview);

                    if (selectedFile.type.indexOf('image') === 0) {
                        pendingPreview.append('<div class="dz-image shadow"><img src="' + localPreviewSource + '"/></div>');
                        applyOneShotImageFallback(pendingPreview.find('img').get(0), '');
                    } else if (selectedFile.type.indexOf('video') === 0) {
                        pendingPreview.append('<div class="video-preview-item shadow"><video class="video-preview" controls muted src="' + localPreviewSource + '"></video></div>');
                    } else if (selectedFile.type.indexOf('audio') === 0) {
                        pendingPreview.append('<div class="audio-preview-item shadow"><audio controls src="' + localPreviewSource + '"></audio></div>');
                    } else {
                        pendingPreview.append('<div class="dz-image shadow"><img src="{{ asset("img/file-icon.png") }}" style="max-width:64px;"/></div>');
                    }

                    var formData = new FormData($('#simple-upload-form')[0]);
                    
                    // Show loading indicator
                    if (typeof launchToast !== 'undefined') {
                        launchToast('info', '{{ __("Upload") }}', '{{ __("Uploading your file...") }}', 'now');
                    }
                    
                    $.ajax({
                        url: $('#simple-upload-form').attr('action'),
                        type: 'POST',
                        data: formData,
                        contentType: false,
                        processData: false,
                        success: function(response) {
                            if (response.success) {
                                var previewSource = normalizePreviewUrl(response.thumbnail || response.path);
                                var fallbackSource = normalizePreviewUrl(response.path || '');

                                // Handle success
                                if (typeof launchToast !== 'undefined') {
                                    launchToast('success', '{{ __("Success") }}', '{{ __("File uploaded successfully!") }}', 'now');
                                }
                                
                                // Add to FileUpload attachments
                                if (typeof FileUpload !== 'undefined') {
                                    FileUpload.attachaments.push({
                                        attachmentID: response.attachmentID,
                                        path: response.path,
                                        type: response.type,
                                        thumbnail: response.thumbnail || response.path
                                    });
                                    
                                    var filePreview = pendingPreview || $('<div class="dz-preview ml-1 mr-2"></div>');
                                    filePreview.removeClass('dz-processing').addClass('dz-success dz-complete');
                                    
                                    // Add preview content based on file type
                                    if (!pendingPreview && response.type === 'image') {
                                        filePreview.append('<div class="dz-image shadow"><img src="' + (previewSource || imageFallbackDataUrl()) + '" data-fallback-src="' + fallbackSource + '"/></div>');
                                        applyOneShotImageFallback(filePreview.find('img').get(0), fallbackSource);
                                    } else if (!pendingPreview && response.type === 'video') {
                                        filePreview.append('<div class="video-preview-item shadow"><video class="video-preview" controls autoplay muted loop src="' + normalizePreviewUrl(response.path) + '"></video></div>');
                                    } else if (!pendingPreview && response.type === 'audio') {
                                        filePreview.append('<div class="audio-preview-item shadow"><audio controls src="' + normalizePreviewUrl(response.path) + '"></audio></div>');
                                    } else if (!pendingPreview) {
                                        filePreview.append('<div class="dz-image shadow"><img src="{{ asset("img/file-icon.png") }}" style="max-width:64px;"/></div>');
                                    }

                                    if (!pendingPreview) {
                                        $('.dropzone-previews').append(filePreview);
                                    }
                                    
                                    // Add remove button
                                    var removeButton = $('<a class="dz-remove" href="javascript:undefined;" data-dz-remove>x</a>');
                                    removeButton.on('click', function() {
                                        if (typeof FileUpload !== 'undefined') {
                                            FileUpload.removeAttachment(response.attachmentID);
                                        }
                                        window.URL.revokeObjectURL(localPreviewSource);
                                        filePreview.remove();
                                    });
                                    filePreview.append(removeButton);
                                }
                            } else {
                                if (typeof launchToast !== 'undefined') {
                                    launchToast('danger', '{{ __("Error") }}', '{{ __("File upload failed.") }}', 'now');
                                } else {
                                    alert('{{ __("File upload failed.") }}');
                                }
                            }
                        },
                        error: function(xhr) {
                            console.error('Upload error:', xhr);
                            if (pendingPreview) {
                                pendingPreview.remove();
                            }
                            if (typeof launchToast !== 'undefined') {
                                launchToast('danger', '{{ __("Error") }}', '{{ __("File upload failed. Please try again.") }}', 'now');
                            } else {
                                alert('{{ __("File upload failed. Please try again.") }}');
                            }
                        },
                        complete: function() {
                            // Reset the file input
                            $('#direct-file-input').val('');
                        }
                    });
                }
            });
            
            // Handle save button functionality
            $(document).off('click', '.submit-button, .save-button').on('click', '.submit-button, .save-button', function(e) {
                console.log('Save button clicked');
                
                // If there's a form associated with the button, check for uploads in progress
                var $form = $(this).closest('form');
                
                if (typeof FileUpload !== 'undefined' && FileUpload.isLoading) {
                    e.preventDefault();
                    
                    if (typeof launchToast !== 'undefined') {
                        launchToast('warning', '{{ __("Please wait") }}', '{{ __("Please wait for file uploads to complete") }}');
                    } else {
                        alert('{{ __("Please wait for file uploads to complete") }}');
                    }
                    return false;
                }
                
                // Continue with default form submission
                if ($form.length && !$form.data('submitting')) {
                    $form.data('submitting', true);
                    console.log('Submitting form:', $form.attr('id'));
                }
            });
        }
    })();
</script> 
