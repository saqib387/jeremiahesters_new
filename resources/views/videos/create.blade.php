@extends('layouts.generic')

@section('page_title')
    {{ __('Upload Video') }}
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm video-upload-card">
                <div class="card-header bg-gradient text-white" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                    <h2 class="mb-0">
                        <i class="fa fa-video-camera mr-2"></i>Upload Video
                    </h2>
                </div>
                <div class="card-body p-4">
                    {{-- Show posting warnings if user doesn't meet requirements --}}
                    @if(isset($postingWarnings) && count($postingWarnings) > 0)
                        <div class="alert alert-warning">
                            <h5 class="alert-heading"><i class="fa fa-exclamation-triangle mr-2"></i>Action Required</h5>
                            <p class="mb-2">To upload videos, please complete the following:</p>
                            <ul class="mb-2">
                                @foreach($postingWarnings as $warning)
                                    <li>{{ $warning }}</li>
                                @endforeach
                            </ul>
                            <hr>
                            <a href="{{ route('my.settings', ['type' => 'verify']) }}" class="btn btn-warning btn-sm">
                                <i class="fa fa-check-circle mr-1"></i>Complete Verification
                            </a>
                        </div>
                    @endif

                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('videos.store') }}" enctype="multipart/form-data" id="videoUploadForm">
                        @csrf
                        
                        {{-- Video File Upload --}}
                        <div class="form-group mb-4">
                            <label for="video" class="font-weight-bold">Video File <span class="text-danger">*</span></label>
                            <div class="upload-zone" id="videoDropZone">
                                <input type="file" class="form-control d-none @error('video') is-invalid @enderror" 
                                       id="video" name="video" accept="video/mp4,video/mov,video/webm,video/avi" required>
                                <div class="upload-placeholder text-center py-5" id="videoPlaceholder">
                                    <i class="fa fa-cloud-upload fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">Drag & drop your video here</h5>
                                    <p class="text-muted small mb-3">or click to browse</p>
                                    <button type="button" class="btn btn-outline-primary btn-sm" onclick="document.getElementById('video').click()">
                                        <i class="fa fa-folder-open mr-2"></i>Choose File
                                    </button>
                                    <p class="text-muted small mt-3 mb-0">
                                        Supported formats: MP4, MOV, WebM, AVI (Max 20MB)
                                    </p>
                                </div>
                                <div class="upload-preview d-none" id="videoPreview">
                                    <video id="videoPreviewPlayer" class="w-100 rounded" style="max-height: 300px;" controls></video>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="text-muted small" id="videoFileName"></span>
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearVideoPreview()">
                                            <i class="fa fa-times mr-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @error('video')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Title --}}
                        <div class="form-group mb-4">
                            <label for="title" class="font-weight-bold">Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" 
                                   placeholder="Enter a catchy title for your video" required maxlength="191">
                            <small class="form-text text-muted">
                                <span id="titleCharCount">0</span>/191 characters
                            </small>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Description --}}
                        <div class="form-group mb-4">
                            <label for="description" class="font-weight-bold">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4" 
                                      placeholder="Tell viewers what your video is about..." maxlength="1000">{{ old('description') }}</textarea>
                            <small class="form-text text-muted">
                                <span id="descCharCount">0</span>/1000 characters
                            </small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Thumbnail Upload --}}
                        <div class="form-group mb-4">
                            <label for="thumbnail" class="font-weight-bold">Thumbnail (Optional)</label>
                            <div class="upload-zone thumbnail-zone" id="thumbnailDropZone">
                                <input type="file" class="form-control d-none @error('thumbnail') is-invalid @enderror" 
                                       id="thumbnail" name="thumbnail" accept="image/jpeg,image/png,image/jpg,image/gif">
                                <div class="upload-placeholder text-center py-4" id="thumbnailPlaceholder">
                                    <i class="fa fa-image fa-2x text-muted mb-2"></i>
                                    <p class="text-muted small mb-2">Click to upload a custom thumbnail</p>
                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="document.getElementById('thumbnail').click()">
                                        <i class="fa fa-upload mr-2"></i>Upload Image
                                    </button>
                                    <p class="text-muted small mt-2 mb-0">
                                        JPEG, PNG, GIF (Max 5MB) - Recommended: 1280x720
                                    </p>
                                </div>
                                <div class="upload-preview d-none" id="thumbnailPreview">
                                    <img id="thumbnailPreviewImg" class="w-100 rounded" style="max-height: 200px; object-fit: cover;">
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="text-muted small" id="thumbnailFileName"></span>
                                        <button type="button" class="btn btn-outline-danger btn-sm" onclick="clearThumbnailPreview()">
                                            <i class="fa fa-times mr-1"></i>Remove
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @error('thumbnail')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Upload Progress --}}
                        <div class="form-group mb-4 d-none" id="uploadProgress">
                            <label class="font-weight-bold">Upload Progress</label>
                            <div class="progress" style="height: 25px;">
                                <div class="progress-bar progress-bar-striped progress-bar-animated" 
                                     role="progressbar" style="width: 0%;" id="progressBar">
                                    <span id="progressText">0%</span>
                                </div>
                            </div>
                            <p class="text-muted small mt-2" id="uploadStatus">Preparing upload...</p>
                        </div>

                        {{-- Submit Buttons --}}
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary btn-lg btn-block mb-2" id="submitBtn" @if(isset($canPost) && !$canPost) disabled @endif>
                                <i class="fa fa-upload mr-2"></i>Upload Video
                            </button>
                            <a href="{{ route('videos.reels') }}" class="btn btn-outline-secondary btn-block">
                                <i class="fa fa-arrow-left mr-2"></i>Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar --}}
        <div class="col-md-4">
            {{-- Upload Guidelines --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fa fa-lightbulb-o text-warning mr-2"></i>Upload Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled mb-0">
                        <li class="mb-3">
                            <i class="fa fa-check-circle text-success mr-2"></i>
                            Use good lighting for better quality
                        </li>
                        <li class="mb-3">
                            <i class="fa fa-check-circle text-success mr-2"></i>
                            Keep videos under 20MB for faster uploads
                        </li>
                        <li class="mb-3">
                            <i class="fa fa-check-circle text-success mr-2"></i>
                            Use vertical format (9:16) for best viewing
                        </li>
                        <li class="mb-3">
                            <i class="fa fa-check-circle text-success mr-2"></i>
                            Write catchy titles to attract viewers
                        </li>
                        <li>
                            <i class="fa fa-check-circle text-success mr-2"></i>
                            Add a custom thumbnail to stand out
                        </li>
                    </ul>
                </div>
            </div>

            {{-- Community Guidelines --}}
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0"><i class="fa fa-shield text-primary mr-2"></i>Guidelines</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info mb-0">
                        <p class="small mb-2">By uploading, you agree to our community guidelines:</p>
                        <ul class="small mb-0 pl-3">
                            <li>No illegal or harmful content</li>
                            <li>Respect copyright and intellectual property</li>
                            <li>No hate speech or harassment</li>
                            <li>Must be appropriate for the platform</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    .video-upload-card .card-header h2 {
        font-size: 1.5rem;
    }
    
    .upload-zone {
        border: 2px dashed #dee2e6;
        border-radius: 10px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
        cursor: pointer;
    }
    
    .upload-zone:hover {
        border-color: #667eea;
        background-color: #f0f4ff;
    }
    
    .upload-zone.dragover {
        border-color: #667eea;
        background-color: #e8ecff;
        transform: scale(1.02);
    }
    
    .upload-zone .upload-placeholder {
        padding: 2rem;
    }
    
    .upload-zone .upload-preview {
        padding: 1rem;
    }
    
    .video-upload-card .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        font-weight: 600;
    }
    
    .video-upload-card .btn-primary:hover {
        background: linear-gradient(135deg, #5a6fd6 0%, #6a4190 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
    }
    
    .video-upload-card .btn-outline-primary {
        border-color: #667eea;
        color: #667eea;
    }
    
    .video-upload-card .btn-outline-primary:hover {
        background-color: #667eea;
        border-color: #667eea;
        color: #fff;
    }
    
    .video-upload-card .progress {
        border-radius: 15px;
        overflow: hidden;
    }
    
    .video-upload-card .progress-bar {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        font-weight: 600;
    }
    
    .video-upload-card .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }
    
    .thumbnail-zone {
        min-height: 150px;
    }
    
    @media (max-width: 768px) {
        .col-md-4 {
            margin-top: 1.5rem;
        }
    }
</style>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Video file handling
    var videoInput = document.getElementById('video');
    var videoDropZone = document.getElementById('videoDropZone');
    var videoPlaceholder = document.getElementById('videoPlaceholder');
    var videoPreview = document.getElementById('videoPreview');
    var videoPreviewPlayer = document.getElementById('videoPreviewPlayer');
    var videoFileName = document.getElementById('videoFileName');
    
    // Thumbnail handling
    var thumbnailInput = document.getElementById('thumbnail');
    var thumbnailDropZone = document.getElementById('thumbnailDropZone');
    var thumbnailPlaceholder = document.getElementById('thumbnailPlaceholder');
    var thumbnailPreview = document.getElementById('thumbnailPreview');
    var thumbnailPreviewImg = document.getElementById('thumbnailPreviewImg');
    var thumbnailFileName = document.getElementById('thumbnailFileName');
    
    // Character counters
    var titleInput = document.getElementById('title');
    var titleCharCount = document.getElementById('titleCharCount');
    var descriptionInput = document.getElementById('description');
    var descCharCount = document.getElementById('descCharCount');
    
    // Video drag and drop
    setupDropZone(videoDropZone, videoInput);
    setupDropZone(thumbnailDropZone, thumbnailInput);
    
    function setupDropZone(dropZone, input) {
        if (!dropZone || !input) return;
        
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(function(eventName) {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });
        
        ['dragenter', 'dragover'].forEach(function(eventName) {
            dropZone.addEventListener(eventName, function() { dropZone.classList.add('dragover'); }, false);
        });
        
        ['dragleave', 'drop'].forEach(function(eventName) {
            dropZone.addEventListener(eventName, function() { dropZone.classList.remove('dragover'); }, false);
        });
        
        dropZone.addEventListener('drop', function(e) {
            var files = e.dataTransfer.files;
            if (files.length) {
                input.files = files;
                var event = new Event('change', { bubbles: true });
                input.dispatchEvent(event);
            }
        });
        
        dropZone.addEventListener('click', function(e) {
            if (e.target.tagName !== 'BUTTON' && e.target.tagName !== 'INPUT' && e.target.tagName !== 'I') {
                input.click();
            }
        });
    }
    
    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }
    
    // Video file change handler
    if (videoInput) {
        videoInput.addEventListener('change', function() {
            var file = this.files[0];
            if (file) {
                // Validate file size (20MB)
                if (file.size > 20 * 1024 * 1024) {
                    alert('Video file is too large. Maximum size is 20MB.');
                    this.value = '';
                    return;
                }
                
                // Show preview
                var url = URL.createObjectURL(file);
                videoPreviewPlayer.src = url;
                videoFileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
                videoPlaceholder.classList.add('d-none');
                videoPreview.classList.remove('d-none');
            }
        });
    }
    
    // Thumbnail file change handler
    if (thumbnailInput) {
        thumbnailInput.addEventListener('change', function() {
            var file = this.files[0];
            if (file) {
                // Validate file size (5MB)
                if (file.size > 5 * 1024 * 1024) {
                    alert('Thumbnail is too large. Maximum size is 5MB.');
                    this.value = '';
                    return;
                }
                
                // Show preview
                var reader = new FileReader();
                reader.onload = function(e) {
                    thumbnailPreviewImg.src = e.target.result;
                    thumbnailFileName.textContent = file.name + ' (' + formatFileSize(file.size) + ')';
                    thumbnailPlaceholder.classList.add('d-none');
                    thumbnailPreview.classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });
    }
    
    // Character counters
    if (titleInput) {
        titleInput.addEventListener('input', function() {
            titleCharCount.textContent = this.value.length;
        });
        titleCharCount.textContent = titleInput.value.length;
    }
    
    if (descriptionInput) {
        descriptionInput.addEventListener('input', function() {
            descCharCount.textContent = this.value.length;
        });
        descCharCount.textContent = descriptionInput.value.length;
    }
    
    // Form submission with progress
    var form = document.getElementById('videoUploadForm');
    var submitBtn = document.getElementById('submitBtn');
    var uploadProgress = document.getElementById('uploadProgress');
    var progressBar = document.getElementById('progressBar');
    var progressText = document.getElementById('progressText');
    var uploadStatus = document.getElementById('uploadStatus');
    
    if (form) {
        form.addEventListener('submit', function(e) {
            // Validate video is selected
            if (!videoInput.files || !videoInput.files[0]) {
                e.preventDefault();
                alert('Please select a video file to upload.');
                return;
            }
            
            // Validate title
            if (!titleInput.value.trim()) {
                e.preventDefault();
                alert('Please enter a title for your video.');
                titleInput.focus();
                return;
            }
            
            // Show progress and disable button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fa fa-spinner fa-spin mr-2"></i>Uploading...';
            uploadProgress.classList.remove('d-none');
            
            // Simulate progress (actual progress would need AJAX)
            var progress = 0;
            var interval = setInterval(function() {
                progress += Math.random() * 15;
                if (progress > 90) progress = 90;
                progressBar.style.width = progress + '%';
                progressText.textContent = Math.round(progress) + '%';
                uploadStatus.textContent = 'Uploading video...';
            }, 500);
            
            // Let form submit normally
        });
    }
    
    // Helper function to format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        var k = 1024;
        var sizes = ['Bytes', 'KB', 'MB', 'GB'];
        var i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }
});

// Clear video preview
function clearVideoPreview() {
    var videoInput = document.getElementById('video');
    var videoPlaceholder = document.getElementById('videoPlaceholder');
    var videoPreview = document.getElementById('videoPreview');
    var videoPreviewPlayer = document.getElementById('videoPreviewPlayer');
    
    videoInput.value = '';
    videoPreviewPlayer.src = '';
    videoPlaceholder.classList.remove('d-none');
    videoPreview.classList.add('d-none');
}

// Clear thumbnail preview
function clearThumbnailPreview() {
    var thumbnailInput = document.getElementById('thumbnail');
    var thumbnailPlaceholder = document.getElementById('thumbnailPlaceholder');
    var thumbnailPreview = document.getElementById('thumbnailPreview');
    var thumbnailPreviewImg = document.getElementById('thumbnailPreviewImg');
    
    thumbnailInput.value = '';
    thumbnailPreviewImg.src = '';
    thumbnailPlaceholder.classList.remove('d-none');
    thumbnailPreview.classList.add('d-none');
}
</script>
@endsection
