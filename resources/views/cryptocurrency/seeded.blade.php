@extends('layouts.generic')

@section('page_title', __('Cryptocurrency Data Seeded'))

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">{{ __('Cryptocurrency Data Seeded Successfully') }}</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-success mb-4">
                        <i class="fas fa-check-circle mr-2"></i>
                        {{ __('Sample cryptocurrency data was generated and added to your database.') }}
                    </div>
                    
                    <h5 class="mb-3">{{ __('Process Details:') }}</h5>
                    <div class="bg-light p-3 rounded mb-4" style="max-height: 400px; overflow-y: auto;">
                        @foreach($output as $line)
                            <div class="mb-1">{{ $line }}</div>
                        @endforeach
                    </div>
                    
                    <div class="d-flex flex-wrap justify-content-between">
                        <a href="{{ route('cryptocurrency.index') }}" class="btn btn-primary mb-2">
                            <i class="fas fa-coins mr-2"></i>{{ __('View Cryptocurrencies') }}
                        </a>
                        <a href="{{ route('cryptocurrency.wallet') }}" class="btn btn-outline-primary mb-2">
                            <i class="fas fa-wallet mr-2"></i>{{ __('View Your Wallet') }}
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="row justify-content-center mt-4">
        <div class="col-12 col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-light">
                    <h5 class="mb-0">{{ __('Next Steps') }}</h5>
                </div>
                <div class="card-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex align-items-center">
                            <span class="badge badge-primary rounded-circle mr-3">1</span>
                            <div>
                                <strong>{{ __('Explore Available Tokens') }}</strong>
                                <p class="mb-0 text-muted">{{ __('Check out the marketplace to see all available cryptocurrencies.') }}</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <span class="badge badge-primary rounded-circle mr-3">2</span>
                            <div>
                                <strong>{{ __('Buy Some Tokens') }}</strong>
                                <p class="mb-0 text-muted">{{ __('Purchase tokens to support creators and unlock premium content.') }}</p>
                            </div>
                        </li>
                        <li class="list-group-item d-flex align-items-center">
                            <span class="badge badge-primary rounded-circle mr-3">3</span>
                            <div>
                                <strong>{{ __('Create Your Own Token') }}</strong>
                                <p class="mb-0 text-muted">{{ __('As a creator, you can launch your own cryptocurrency for your community.') }}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection 