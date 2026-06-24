@extends('layouts.generic')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6">
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h4 class="mb-0">Upload Error</h4>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <i class="fas fa-exclamation-triangle text-danger" style="font-size: 60px;"></i>
                    </div>
                    
                    <h5 class="card-title text-center mb-4">We encountered an issue with your file upload</h5>
                    
                    <div class="alert alert-secondary">
                        <p><strong>Error details:</strong> {{ $errorMessage ?? 'Unknown error' }}</p>
                    </div>
                    
                    <h6 class="mt-4 mb-3">Common solutions:</h6>
                    <ul>
                        <li>Rename your file to use only English letters, numbers, and common symbols</li>
                        <li>Try a different image file (JPG or PNG formats work best)</li>
                        <li>Make sure your file is not corrupted</li>
                        <li>Reduce the file size if it's very large</li>
                        <li>Clear your browser cache and try again</li>
                    </ul>
                    
                    <div class="d-grid gap-2 mt-4">
                        <a href="javascript:history.back();" class="btn btn-primary">Go Back</a>
                        <a href="{{ url('/') }}" class="btn btn-outline-secondary">Go to Home</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 