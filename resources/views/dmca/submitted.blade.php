@extends('layouts.generic')

@section('page_title', __('Request Submitted'))

@section('styles')
<style>
    .confirmation-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #0a0a0a 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 40px 20px;
    }
    
    .confirmation-card {
        max-width: 600px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 50px;
        text-align: center;
        backdrop-filter: blur(20px);
    }
    
    .success-icon {
        width: 100px;
        height: 100px;
        background: rgba(34, 197, 94, 0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 30px;
    }
    
    .success-icon i {
        font-size: 50px;
        color: #22c55e;
    }
    
    .confirmation-card h1 {
        color: #fff;
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 15px 0;
    }
    
    .confirmation-card p {
        color: rgba(255, 255, 255, 0.7);
        font-size: 16px;
        line-height: 1.6;
        margin-bottom: 20px;
    }
    
    .reference-box {
        background: rgba(131, 8, 102, 0.2);
        border: 1px solid rgba(131, 8, 102, 0.3);
        border-radius: 12px;
        padding: 20px;
        margin: 30px 0;
    }
    
    .reference-box .label {
        color: rgba(255, 255, 255, 0.6);
        font-size: 14px;
        margin-bottom: 8px;
    }
    
    .reference-box .number {
        color: #fff;
        font-size: 24px;
        font-weight: 700;
        font-family: monospace;
    }
    
    .next-steps {
        text-align: left;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 12px;
        padding: 25px;
        margin: 30px 0;
    }
    
    .next-steps h3 {
        color: #fff;
        font-size: 18px;
        margin: 0 0 15px 0;
    }
    
    .next-steps ul {
        margin: 0;
        padding-left: 20px;
    }
    
    .next-steps li {
        color: rgba(255, 255, 255, 0.8);
        margin-bottom: 10px;
        line-height: 1.5;
    }
    
    .btn-back {
        display: inline-flex;
        align-items: center;
        gap: 10px;
        padding: 14px 28px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        color: #fff;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .btn-back:hover {
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="confirmation-container">
    <div class="confirmation-card">
        <div class="success-icon">
            <i class="fas fa-check"></i>
        </div>
        
        <h1>{{ __('Request Submitted Successfully') }}</h1>
        <p>{{ __('Your DMCA takedown request has been received and will be reviewed by our designated agent.') }}</p>
        
        @if(session('reference_number'))
            <div class="reference-box">
                <div class="label">{{ __('Reference Number') }}</div>
                <div class="number">{{ session('reference_number') }}</div>
            </div>
        @endif
        
        <div class="next-steps">
            <h3><i class="fas fa-clipboard-list"></i> {{ __('What Happens Next?') }}</h3>
            <ul>
                <li>{{ __('Our team will review your request within 24-48 hours.') }}</li>
                <li>{{ __('If your request is valid, the content will be removed or disabled.') }}</li>
                <li>{{ __('The content owner will be notified and may submit a counter-notification.') }}</li>
                <li>{{ __('You will receive email updates about the status of your request.') }}</li>
            </ul>
        </div>
        
        <p style="color: rgba(255,255,255,0.5); font-size: 14px;">
            {{ __('Please save your reference number for future correspondence.') }}
        </p>
        
        <a href="{{ route('dmca.index') }}" class="btn-back">
            <i class="fas fa-arrow-left"></i>
            {{ __('Back to DMCA Policy') }}
        </a>
    </div>
</div>
@endsection
