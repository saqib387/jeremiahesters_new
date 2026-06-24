@extends('layouts.generic')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="mb-0">Start Livestream</h2>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('streams.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="mb-4">
                            <label for="title" class="form-label">Stream Title *</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" id="title" name="title" value="{{ old('title') }}" required>
                            <div class="form-text">Choose a catchy title for your livestream</div>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="3">{{ old('description') }}</textarea>
                            <div class="form-text">Tell viewers what your stream is about</div>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <label for="thumbnail" class="form-label">Stream Thumbnail</label>
                            <input type="file" class="form-control @error('thumbnail') is-invalid @enderror" id="thumbnail" name="thumbnail" accept="image/*">
                            <div class="form-text">Recommended size: 1280x720px (16:9 ratio)</div>
                            @error('thumbnail')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="requires_subscription" name="requires_subscription" {{ old('requires_subscription') ? 'checked' : '' }}>
                                <label class="form-check-label" for="requires_subscription">Subscribers Only</label>
                            </div>
                            <div class="form-text">Only subscribers will be able to view your stream</div>
                        </div>
                        
                        <div class="mb-4">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_public" name="is_public" {{ old('is_public', '1') ? 'checked' : '' }}>
                                <label class="form-check-label" for="is_public">Public Stream</label>
                            </div>
                            <div class="form-text">Your stream will appear in public listings</div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="price" class="form-label">Stream Price (optional)</label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" class="form-control @error('price') is-invalid @enderror" id="price" name="price" value="{{ old('price', '0') }}" min="0" step="0.01">
                            </div>
                            <div class="form-text">Set to 0 for free access</div>
                            @error('price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-4">
                            <div class="alert alert-info">
                                <h5 class="alert-heading"><i class="fas fa-info-circle"></i> Streaming Guidelines</h5>
                                <p class="mb-0">By starting a livestream, you agree to our community guidelines:</p>
                                <ul class="mb-0">
                                    <li>No illegal content or activities</li>
                                    <li>Be respectful to all viewers</li>
                                    <li>No hateful or discriminatory content</li>
                                    <li>Streams may be monitored for compliance</li>
                                </ul>
                            </div>
                        </div>
                        
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">Start Streaming</button>
                            <a href="{{ route('streams.index') }}" class="btn btn-outline-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Streaming Tips</h5>
                </div>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Test your internet connection before going live
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Make sure you have good lighting
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Use a quality microphone for better audio
                        </li>
                        <li class="mb-3">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Interact with your viewers regularly
                        </li>
                        <li>
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Promote your stream on social media
                        </li>
                    </ul>
                </div>
            </div>
            
            <div class="card shadow-sm">
                <div class="card-header bg-white">
                    <h5 class="mb-0">Recommended Software</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://obsproject.com/assets/images/new_icon_small-r.png" alt="OBS Studio" class="me-2" width="32">
                        <div>
                            <h6 class="mb-0">OBS Studio</h6>
                            <a href="https://obsproject.com/" target="_blank" class="small">Download</a>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <img src="https://streamlabs.com/favicon.ico" alt="Streamlabs" class="me-2" width="32">
                        <div>
                            <h6 class="mb-0">Streamlabs</h6>
                            <a href="https://streamlabs.com/" target="_blank" class="small">Download</a>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <img src="https://cdn.xsplit.com/assets/images/favicons/favicon-32x32.png" alt="XSplit" class="me-2" width="32">
                        <div>
                            <h6 class="mb-0">XSplit</h6>
                            <a href="https://www.xsplit.com/" target="_blank" class="small">Download</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .form-label {
        font-weight: 500;
    }
    
    .card {
        border: none;
        border-radius: 10px;
        overflow: hidden;
    }
    
    .btn-primary {
        background-color: #4A6CF7;
        border-color: #4A6CF7;
        font-weight: 500;
    }
    
    .btn-primary:hover {
        background-color: #3955cf;
        border-color: #3955cf;
    }
</style>
@endpush
@endsection 