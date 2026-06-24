@extends('layouts.generic')

@section('page_title', __('DMCA & Copyright Policy'))

@section('styles')
<style>
    .dmca-container {
        min-height: 100vh;
        background: linear-gradient(135deg, #0a0a0a 0%, #1a1a2e 50%, #0a0a0a 100%);
        padding: 40px 20px;
    }
    
    .dmca-content {
        max-width: 900px;
        margin: 0 auto;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 24px;
        border: 1px solid rgba(255, 255, 255, 0.1);
        overflow: hidden;
        backdrop-filter: blur(20px);
    }
    
    .dmca-header {
        padding: 40px;
        background: linear-gradient(135deg, rgba(131, 8, 102, 0.3), rgba(161, 10, 127, 0.2));
        text-align: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .dmca-header h1 {
        color: #fff;
        font-size: 32px;
        font-weight: 700;
        margin: 0 0 10px 0;
    }
    
    .dmca-header p {
        color: rgba(255, 255, 255, 0.7);
        margin: 0;
        font-size: 16px;
    }
    
    .dmca-body {
        padding: 40px;
        color: rgba(255, 255, 255, 0.9);
        line-height: 1.8;
    }
    
    .dmca-body h2 {
        color: #fff;
        font-size: 24px;
        margin: 30px 0 15px 0;
        padding-bottom: 10px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    
    .dmca-body h2:first-child {
        margin-top: 0;
    }
    
    .dmca-body h3 {
        color: #fff;
        font-size: 18px;
        margin: 25px 0 10px 0;
    }
    
    .dmca-body p {
        margin-bottom: 15px;
    }
    
    .dmca-body ul, .dmca-body ol {
        margin-bottom: 20px;
        padding-left: 25px;
    }
    
    .dmca-body li {
        margin-bottom: 10px;
    }
    
    .agent-info {
        background: rgba(131, 8, 102, 0.2);
        border: 1px solid rgba(131, 8, 102, 0.3);
        border-radius: 12px;
        padding: 25px;
        margin: 25px 0;
    }
    
    .agent-info h3 {
        color: #fff;
        margin-top: 0;
    }
    
    .agent-info p {
        margin-bottom: 5px;
    }
    
    .action-buttons {
        display: flex;
        gap: 15px;
        margin-top: 30px;
        flex-wrap: wrap;
    }
    
    .btn-dmca {
        padding: 14px 28px;
        border-radius: 12px;
        font-size: 16px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 10px;
        transition: all 0.3s ease;
    }
    
    .btn-dmca-primary {
        background: linear-gradient(135deg, #830866, #a10a7f);
        color: #fff;
        border: none;
    }
    
    .btn-dmca-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 30px rgba(131, 8, 102, 0.4);
        color: #fff;
    }
    
    .btn-dmca-secondary {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        color: #fff;
    }
    
    .btn-dmca-secondary:hover {
        background: rgba(255, 255, 255, 0.15);
        color: #fff;
    }
    
    .warning-box {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid rgba(239, 68, 68, 0.3);
        border-radius: 12px;
        padding: 20px;
        margin: 25px 0;
    }
    
    .warning-box h4 {
        color: #ef4444;
        margin: 0 0 10px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    
    .info-box {
        background: rgba(59, 130, 246, 0.1);
        border: 1px solid rgba(59, 130, 246, 0.3);
        border-radius: 12px;
        padding: 20px;
        margin: 25px 0;
    }
    
    .info-box h4 {
        color: #3b82f6;
        margin: 0 0 10px 0;
        display: flex;
        align-items: center;
        gap: 10px;
    }
</style>
@endsection

@section('content')
<div class="dmca-container">
    <div class="dmca-content">
        <div class="dmca-header">
            <h1><i class="fas fa-shield-alt"></i> {{ __('DMCA & Copyright Policy') }}</h1>
            <p>{{ __('Digital Millennium Copyright Act Notice & Takedown Policy') }}</p>
        </div>
        
        <div class="dmca-body">
            <h2>{{ __('Introduction') }}</h2>
            <p>{{ getSetting('site.name') }} ("we", "us", or "our") respects the intellectual property rights of others and expects its users to do the same. In accordance with the Digital Millennium Copyright Act of 1998 ("DMCA"), we will respond expeditiously to claims of copyright infringement committed using our service.</p>
            
            <h2>{{ __('DMCA Designated Agent') }}</h2>
            <div class="agent-info">
                <h3><i class="fas fa-user-tie"></i> {{ __('Designated Agent for DMCA Notices') }}</h3>
                <p><strong>{{ __('Name') }}:</strong> {{ getSetting('dmca.agent_name') ?? getSetting('site.name') . ' Legal Team' }}</p>
                <p><strong>{{ __('Email') }}:</strong> {{ getSetting('dmca.agent_email') ?? 'dmca@' . parse_url(config('app.url'), PHP_URL_HOST) }}</p>
                <p><strong>{{ __('Address') }}:</strong> {{ getSetting('dmca.agent_address') ?? getSetting('site.address') ?? 'Contact us for address' }}</p>
            </div>
            
            <h2>{{ __('Filing a DMCA Takedown Notice') }}</h2>
            <p>{{ __('If you believe that your copyrighted work has been copied in a way that constitutes copyright infringement, please provide our designated agent with the following information:') }}</p>
            
            <ol>
                <li>{{ __('A physical or electronic signature of a person authorized to act on behalf of the owner of an exclusive right that is allegedly infringed.') }}</li>
                <li>{{ __('Identification of the copyrighted work claimed to have been infringed, or if multiple copyrighted works are covered by a single notification, a representative list of such works.') }}</li>
                <li>{{ __('Identification of the material that is claimed to be infringing or to be the subject of infringing activity and that is to be removed or access to which is to be disabled, and information reasonably sufficient to permit us to locate the material.') }}</li>
                <li>{{ __('Information reasonably sufficient to permit us to contact you, such as an address, telephone number, and email address.') }}</li>
                <li>{{ __('A statement that you have a good faith belief that use of the material in the manner complained of is not authorized by the copyright owner, its agent, or the law.') }}</li>
                <li>{{ __('A statement that the information in the notification is accurate, and under penalty of perjury, that you are authorized to act on behalf of the owner of an exclusive right that is allegedly infringed.') }}</li>
            </ol>
            
            <div class="warning-box">
                <h4><i class="fas fa-exclamation-triangle"></i> {{ __('Important Warning') }}</h4>
                <p>{{ __('Under Section 512(f) of the DMCA, any person who knowingly materially misrepresents that material is infringing may be subject to liability for damages.') }}</p>
            </div>
            
            <div class="action-buttons">
                <a href="{{ route('dmca.takedown-form') }}" class="btn-dmca btn-dmca-primary">
                    <i class="fas fa-file-alt"></i>
                    {{ __('File a Takedown Request') }}
                </a>
            </div>
            
            <h2>{{ __('Counter-Notification') }}</h2>
            <p>{{ __('If you believe that your content was removed or disabled by mistake or misidentification, you may file a counter-notification with the following information:') }}</p>
            
            <ol>
                <li>{{ __('Your physical or electronic signature.') }}</li>
                <li>{{ __('Identification of the material that has been removed or to which access has been disabled and the location at which the material appeared before it was removed or disabled.') }}</li>
                <li>{{ __('A statement under penalty of perjury that you have a good faith belief that the material was removed or disabled as a result of mistake or misidentification.') }}</li>
                <li>{{ __('Your name, address, and telephone number, and a statement that you consent to the jurisdiction of the Federal District Court and that you will accept service of process.') }}</li>
            </ol>
            
            <div class="action-buttons">
                <a href="{{ route('dmca.counter-notification') }}" class="btn-dmca btn-dmca-secondary">
                    <i class="fas fa-reply"></i>
                    {{ __('File a Counter-Notification') }}
                </a>
            </div>
            
            <h2>{{ __('Repeat Infringers Policy') }}</h2>
            <p>{{ __('In accordance with the DMCA and other applicable law, we have adopted a policy of terminating, in appropriate circumstances, users who are deemed to be repeat infringers. We may also, at our sole discretion, limit access to our service and/or terminate the accounts of any users who infringe any intellectual property rights of others, whether or not there is any repeat infringement.') }}</p>
            
            <div class="info-box">
                <h4><i class="fas fa-info-circle"></i> {{ __('Response Time') }}</h4>
                <p>{{ __('We strive to process all valid DMCA takedown requests within 24-48 hours. However, complex cases may require additional time for proper review.') }}</p>
            </div>
            
            <h2>{{ __('Contact Us') }}</h2>
            <p>{{ __('If you have any questions about our DMCA policy, please contact us at:') }}</p>
            <p><strong>{{ __('Email') }}:</strong> {{ getSetting('dmca.agent_email') ?? 'dmca@' . parse_url(config('app.url'), PHP_URL_HOST) }}</p>
            
            <p style="margin-top: 30px; color: rgba(255,255,255,0.5); font-size: 14px;">
                {{ __('Last updated') }}: {{ date('F j, Y') }}
            </p>
        </div>
    </div>
</div>
@endsection
