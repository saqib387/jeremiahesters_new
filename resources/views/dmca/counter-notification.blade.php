@extends('layouts.generic')

@section('page_title', __('DMCA Counter-Notification'))

@section('styles')
<style>
    .dmca-form-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #0a0a0a 100%);
        padding: 40px 20px;
    }
    
    .dmca-form-card {
        max-width: 800px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        overflow: hidden;
        backdrop-filter: blur(20px);
    }
    
    .form-header {
        padding: 30px;
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.3), rgba(37, 99, 235, 0.2));
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .form-header h1 {
        color: #fff;
        font-size: 28px;
        font-weight: 700;
        margin: 0 0 10px 0;
    }
    
    .form-header p {
        color: rgba(255, 255, 255, 0.7);
        margin: 0;
        font-size: 14px;
    }
    
    .form-body {
        padding: 30px;
    }
    
    .form-section {
        margin-bottom: 30px;
        padding-bottom: 30px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .form-section-title {
        color: #fff;
        font-size: 18px;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .form-section-title i {
        color: #3b82f6;
    }
    
    .form-group {
        margin-bottom: 20px;
    }
    
    .form-group label {
        display: block;
        color: #fff;
        font-size: 14px;
        font-weight: 500;
        margin-bottom: 8px;
    }
    
    .form-group label .required {
        color: #ef4444;
    }
    
    .form-control {
        width: 100%;
        padding: 14px 16px;
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 10px;
        color: #fff;
        font-size: 14px;
        transition: all 0.3s ease;
        box-sizing: border-box;
    }
    
    .form-control:focus {
        outline: none;
        border-color: #3b82f6;
        box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.2);
    }
    
    textarea.form-control {
        min-height: 120px;
        resize: vertical;
    }
    
    .form-text {
        font-size: 12px;
        color: rgba(255, 255, 255, 0.5);
        margin-top: 6px;
    }
    
    .checkbox-group {
        display: flex;
        align-items: flex-start;
        gap: 12px;
        padding: 16px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        margin-bottom: 15px;
    }
    
    .checkbox-group input[type="checkbox"] {
        margin-top: 3px;
        width: 18px;
        height: 18px;
        accent-color: #3b82f6;
    }
    
    .checkbox-group label {
        margin: 0;
        line-height: 1.5;
        color: rgba(255, 255, 255, 0.9);
        cursor: pointer;
    }
    
    .info-notice {
        background: rgba(59, 130, 246, 0.15);
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }
    
    .info-notice h4 {
        color: #3b82f6;
        margin: 0 0 10px 0;
        font-size: 16px;
    }
    
    .info-notice p {
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
        font-size: 14px;
        line-height: 1.6;
    }
    
    .btn-submit {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border: none;
        border-radius: 12px;
        color: #fff;
        font-size: 16px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    
    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
    }
    
    .grid-2 {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 20px;
    }
    
    @media (max-width: 600px) {
        .grid-2 {
            grid-template-columns: 1fr;
        }
    }
</style>
@endsection

@section('content')
<div class="dmca-form-container">
    <div class="dmca-form-card">
        <div class="form-header">
            <h1><i class="fas fa-reply"></i> {{ __('DMCA Counter-Notification') }}</h1>
            <p>{{ __('Contest a DMCA takedown notice if you believe it was filed in error') }}</p>
        </div>
        
        <div class="form-body">
            @if($errors->any())
                <div style="background: rgba(239, 68, 68, 0.2); border: 1px solid rgba(239, 68, 68, 0.5); padding: 16px; border-radius: 10px; margin-bottom: 20px;">
                    <ul style="margin: 0; padding-left: 20px; color: #fca5a5;">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            
            <div class="info-notice">
                <h4><i class="fas fa-info-circle"></i> {{ __('Important Information') }}</h4>
                <p>{{ __('A counter-notification is a legal document. If you submit a counter-notification, the complaining party may take legal action against you in federal court. By submitting this form, you consent to jurisdiction in federal court.') }}</p>
            </div>
            
            <form action="{{ route('dmca.submit-counter-notification') }}" method="POST">
                @csrf
                
                <!-- Your Information -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-user"></i>
                        {{ __('Your Information') }}
                    </h3>
                    
                    <div class="grid-2">
                        <div class="form-group">
                            <label>{{ __('Full Legal Name') }} <span class="required">*</span></label>
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required>
                        </div>
                        
                        <div class="form-group">
                            <label>{{ __('Email Address') }} <span class="required">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>{{ __('Mailing Address') }} <span class="required">*</span></label>
                        <textarea name="address" class="form-control" required>{{ old('address') }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>{{ __('Phone Number') }} <span class="required">*</span></label>
                        <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" required>
                    </div>
                </div>
                
                <!-- Original Takedown Reference -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-file-alt"></i>
                        {{ __('Original Takedown Reference') }}
                    </h3>
                    
                    <div class="form-group">
                        <label>{{ __('Original Takedown Reference Number') }} <span class="required">*</span></label>
                        <input type="text" name="original_takedown_reference" class="form-control" value="{{ old('original_takedown_reference') }}" required placeholder="DMCA-XXXXXXXX-XXXXXXXX">
                        <p class="form-text">{{ __('The reference number from the original takedown notice') }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label>{{ __('URL of Removed Content') }} <span class="required">*</span></label>
                        <input type="url" name="content_url" class="form-control" value="{{ old('content_url') }}" required>
                        <p class="form-text">{{ __('The URL where your content was located before removal') }}</p>
                    </div>
                </div>
                
                <!-- Reason for Restoration -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-balance-scale-left"></i>
                        {{ __('Reason for Restoration') }}
                    </h3>
                    
                    <div class="form-group">
                        <label>{{ __('Explain Why the Content Should Be Restored') }} <span class="required">*</span></label>
                        <textarea name="reason_for_restoration" class="form-control" required style="min-height: 200px;" placeholder="Explain in detail why you believe the content was removed by mistake or misidentification...">{{ old('reason_for_restoration') }}</textarea>
                        <p class="form-text">{{ __('Provide detailed explanation (minimum 100 characters)') }}</p>
                    </div>
                </div>
                
                <!-- Sworn Statements -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-gavel"></i>
                        {{ __('Sworn Statements') }}
                    </h3>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" name="perjury_statement" id="perjury_statement" value="1" required>
                        <label for="perjury_statement">
                            {{ __('I swear, under penalty of perjury, that I have a good faith belief that the material was removed or disabled as a result of mistake or misidentification of the material to be removed or disabled.') }} <span class="required">*</span>
                        </label>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" name="jurisdiction_consent" id="jurisdiction_consent" value="1" required>
                        <label for="jurisdiction_consent">
                            {{ __('I consent to the jurisdiction of the Federal District Court for the judicial district in which my address is located, or if my address is outside of the United States, for any judicial district in which the service provider may be found, and I will accept service of process from the person who provided notification or an agent of such person.') }} <span class="required">*</span>
                        </label>
                    </div>
                </div>
                
                <!-- Electronic Signature -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-signature"></i>
                        {{ __('Electronic Signature') }}
                    </h3>
                    
                    <div class="grid-2">
                        <div class="form-group">
                            <label>{{ __('Electronic Signature') }} <span class="required">*</span></label>
                            <input type="text" name="signature" class="form-control" value="{{ old('signature') }}" required placeholder="Type your full legal name">
                        </div>
                        
                        <div class="form-group">
                            <label>{{ __('Date') }} <span class="required">*</span></label>
                            <input type="date" name="signature_date" class="form-control" value="{{ old('signature_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    {{ __('Submit Counter-Notification') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
