@extends('layouts.generic')

@section('page_title', __('DMCA Takedown Request'))

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
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.3), rgba(185, 28, 28, 0.2));
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
    
    .form-section:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
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
        color: #830866;
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
        border-color: #830866;
        box-shadow: 0 0 0 3px rgba(131, 8, 102, 0.2);
    }
    
    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.4);
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
        accent-color: #830866;
    }
    
    .checkbox-group label {
        margin: 0;
        line-height: 1.5;
        color: rgba(255, 255, 255, 0.9);
        cursor: pointer;
    }
    
    .warning-notice {
        background: rgba(239, 68, 68, 0.15);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 12px;
        padding: 20px;
        margin-bottom: 25px;
    }
    
    .warning-notice h4 {
        color: #ef4444;
        margin: 0 0 10px 0;
        font-size: 16px;
    }
    
    .warning-notice p {
        color: rgba(255, 255, 255, 0.8);
        margin: 0;
        font-size: 14px;
        line-height: 1.6;
    }
    
    .btn-submit {
        width: 100%;
        padding: 16px;
        background: linear-gradient(135deg, #ef4444, #b91c1c);
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
        box-shadow: 0 10px 30px rgba(239, 68, 68, 0.3);
    }
    
    .error-message {
        color: #ef4444;
        font-size: 13px;
        margin-top: 6px;
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
            <h1><i class="fas fa-gavel"></i> {{ __('DMCA Takedown Request') }}</h1>
            <p>{{ __('Submit a formal copyright infringement notification') }}</p>
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
            
            <div class="warning-notice">
                <h4><i class="fas fa-exclamation-triangle"></i> {{ __('Legal Notice') }}</h4>
                <p>{{ __('By submitting this notice, you are certifying under penalty of perjury that the information provided is accurate and that you are authorized to act on behalf of the copyright owner. Knowingly making material misrepresentations may subject you to liability for damages.') }}</p>
            </div>
            
            <form action="{{ route('dmca.submit-takedown') }}" method="POST">
                @csrf
                
                <!-- Claimant Information -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-user"></i>
                        {{ __('Claimant Information') }}
                    </h3>
                    
                    <div class="grid-2">
                        <div class="form-group">
                            <label>{{ __('Full Legal Name') }} <span class="required">*</span></label>
                            <input type="text" name="full_name" class="form-control" value="{{ old('full_name') }}" required placeholder="Your full legal name">
                        </div>
                        
                        <div class="form-group">
                            <label>{{ __('Email Address') }} <span class="required">*</span></label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required placeholder="your@email.com">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>{{ __('Company/Organization') }}</label>
                        <input type="text" name="company" class="form-control" value="{{ old('company') }}" placeholder="Company name (if applicable)">
                    </div>
                    
                    <div class="form-group">
                        <label>{{ __('Mailing Address') }} <span class="required">*</span></label>
                        <textarea name="address" class="form-control" required placeholder="Your complete mailing address">{{ old('address') }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>{{ __('Phone Number') }} <span class="required">*</span></label>
                        <input type="tel" name="phone" class="form-control" value="{{ old('phone') }}" required placeholder="+1 (555) 123-4567">
                    </div>
                </div>
                
                <!-- Infringing Content -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-link"></i>
                        {{ __('Infringing Content') }}
                    </h3>
                    
                    <div class="form-group">
                        <label>{{ __('URL of Infringing Content') }} <span class="required">*</span></label>
                        <input type="url" name="content_url" class="form-control" value="{{ old('content_url') }}" required placeholder="https://example.com/infringing-content">
                        <p class="form-text">{{ __('Provide the exact URL where the infringing material can be found') }}</p>
                    </div>
                    
                    <div class="form-group">
                        <label>{{ __('Description of Infringing Material') }} <span class="required">*</span></label>
                        <textarea name="infringing_material_description" class="form-control" required placeholder="Describe in detail what content is infringing your copyright...">{{ old('infringing_material_description') }}</textarea>
                        <p class="form-text">{{ __('Be specific about which material is infringing and how') }}</p>
                    </div>
                </div>
                
                <!-- Original Work -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-copyright"></i>
                        {{ __('Your Original Work') }}
                    </h3>
                    
                    <div class="form-group">
                        <label>{{ __('Description of Your Copyrighted Work') }} <span class="required">*</span></label>
                        <textarea name="original_work_description" class="form-control" required placeholder="Describe your original copyrighted work that has been infringed...">{{ old('original_work_description') }}</textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>{{ __('URL of Your Original Work') }}</label>
                        <input type="url" name="original_work_url" class="form-control" value="{{ old('original_work_url') }}" placeholder="https://your-website.com/original-work">
                        <p class="form-text">{{ __('Optional: Link to where your original work can be found') }}</p>
                    </div>
                </div>
                
                <!-- Sworn Statements -->
                <div class="form-section">
                    <h3 class="form-section-title">
                        <i class="fas fa-balance-scale"></i>
                        {{ __('Sworn Statements') }}
                    </h3>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" name="good_faith_statement" id="good_faith_statement" value="1" required>
                        <label for="good_faith_statement">
                            {{ __('I have a good faith belief that the use of the material in the manner complained of is not authorized by the copyright owner, its agent, or the law.') }} <span class="required">*</span>
                        </label>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" name="accuracy_statement" id="accuracy_statement" value="1" required>
                        <label for="accuracy_statement">
                            {{ __('The information in this notification is accurate, and under penalty of perjury, I am the owner, or an agent authorized to act on behalf of the owner, of an exclusive right that is allegedly infringed.') }} <span class="required">*</span>
                        </label>
                    </div>
                    
                    <div class="checkbox-group">
                        <input type="checkbox" name="authorization_statement" id="authorization_statement" value="1" required>
                        <label for="authorization_statement">
                            {{ __('I am authorized to act on behalf of the owner of the copyright that is allegedly infringed.') }} <span class="required">*</span>
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
                            <p class="form-text">{{ __('By typing your name, you are signing this document electronically') }}</p>
                        </div>
                        
                        <div class="form-group">
                            <label>{{ __('Date') }} <span class="required">*</span></label>
                            <input type="date" name="signature_date" class="form-control" value="{{ old('signature_date', date('Y-m-d')) }}" required>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i>
                    {{ __('Submit Takedown Request') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
