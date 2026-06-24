@extends('layouts.generic')

@section('page_title', __('Edit Token - ') . $cryptocurrency->name)

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12 mb-4">
            <div class="d-flex align-items-center">
                <a href="{{ route('cryptocurrency.show', $cryptocurrency->id) }}" class="btn btn-outline-secondary mr-3">
                    <i class="ion-ios-arrow-back"></i> {{ __('Back to Token') }}
                </a>
                <h2 class="mb-0">{{ __('Edit Token') }}</h2>
            </div>
        </div>
        
        <div class="col-12 col-md-4 mb-4">
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Token Information') }}</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        @if($cryptocurrency->logo)
                            <img src="{{ Storage::disk('public')->url($cryptocurrency->logo) }}" alt="{{ $cryptocurrency->name }}" class="rounded-circle mr-3" width="64" height="64">
                        @else
                            <div class="rounded-circle bg-light d-flex align-items-center justify-content-center mr-3" style="width: 64px; height: 64px;">
                                <h3 class="mb-0 text-primary">{{ strtoupper(substr($cryptocurrency->symbol, 0, 1)) }}</h3>
                            </div>
                        @endif
                        <div>
                            <h4 class="mb-0">{{ $cryptocurrency->name }}</h4>
                            <span class="badge badge-light">{{ strtoupper($cryptocurrency->symbol) }}</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Current Price') }}</span>
                            <span>${{ number_format($cryptocurrency->current_price, 8) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Initial Price') }}</span>
                            <span>${{ number_format($cryptocurrency->initial_price, 8) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Total Supply') }}</span>
                            <span>{{ number_format($cryptocurrency->total_supply) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ __('Available Supply') }}</span>
                            <span>{{ number_format($cryptocurrency->available_supply) }}</span>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <small>{{ __('Note: Some token properties like symbol, initial price, total supply, and blockchain network cannot be changed after creation.') }}</small>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-12 col-md-8">
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">{{ __('Edit Token Details') }}</h5>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0 pl-3">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    
                    <form method="POST" action="{{ route('cryptocurrency.update', $cryptocurrency->id) }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="form-group">
                            <label for="name">{{ __('Name') }}</label>
                            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $cryptocurrency->name) }}" required>
                            @error('name')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                        </div>
                        
                        <div class="form-group">
                            <label for="description">{{ __('Description') }}</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" id="description" name="description" rows="4">{{ old('description', $cryptocurrency->description) }}</textarea>
                            @error('description')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            <small class="text-muted">{{ __('Describe your token and its purpose.') }}</small>
                        </div>
                        
                        <div class="form-group">
                            <label for="logo">{{ __('Logo') }}</label>
                            <div class="custom-file">
                                <input type="file" class="custom-file-input @error('logo') is-invalid @enderror" id="logo" name="logo">
                                <label class="custom-file-label" for="logo">{{ __('Choose file...') }}</label>
                            </div>
                            @error('logo')
                                <span class="invalid-feedback" role="alert">
                                    <strong>{{ $message }}</strong>
                                </span>
                            @enderror
                            @if($cryptocurrency->logo)
                                <div class="mt-2">
                                    <img src="{{ Storage::disk('public')->url($cryptocurrency->logo) }}" alt="{{ $cryptocurrency->name }}" class="rounded" width="64" height="64">
                                    <small class="d-block text-muted">{{ __('Current logo') }}</small>
                                </div>
                            @endif
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">{{ __('Update Token') }}</button>
                            <a href="{{ route('cryptocurrency.show', $cryptocurrency->id) }}" class="btn btn-link">{{ __('Cancel') }}</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.custom-file-input').on('change', function() {
            var fileName = $(this).val().split('\\').pop();
            $(this).next('.custom-file-label').html(fileName);
        });
    });
</script>
@endsection 