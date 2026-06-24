@extends('layouts.no-nav')
@section('page_title', __('Register'))

@section('page_description', getSetting('site.description'))
@section('share_url', route('home'))
@section('share_title', getSetting('site.name') . ' - ' .  __('Register'))
@section('share_description', getSetting('site.description'))
@section('share_type', 'article')
@section('share_img', GenericHelper::getOGMetaImage())

@if(getSetting('security.recaptcha_enabled') && !Auth::check())
    @section('meta')
        {!! NoCaptcha::renderJs() !!}
    @stop
@endif

@section('styles')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        * {
            font-family: 'Inter', sans-serif;
        }
        .auth-page-container {
            min-height: 100vh;
            background: linear-gradient(to bottom right, #000000, #1a1a1a, #000000);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .auth-card {
            width: 100%;
            max-width: 450px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 32px;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        .auth-logo-section {
            text-align: center;
            margin-bottom: 32px;
        }
        .auth-logo-section img {
            height: 48px;
            margin: 0 auto 16px;
            display: block;
        }
        .auth-title {
            font-size: 28px;
            font-weight: bold;
            color: #ffffff;
            margin-bottom: 8px;
        }
        .auth-subtitle {
            color: #9ca3af;
        }
        .auth-footer-links {
            text-align: center;
            margin-top: 24px;
        }
        .auth-footer-links p {
            color: #9ca3af;
            font-size: 14px;
        }
        .auth-footer-links a {
            color: #830866;
            font-weight: 600;
            text-decoration: none;
        }
        .auth-footer-links a:hover {
            color: #a10a7f;
        }
        input::placeholder {
            color: #9ca3af !important;
            opacity: 1;
        }
        input::-webkit-input-placeholder {
            color: #9ca3af !important;
        }
        input::-moz-placeholder {
            color: #9ca3af !important;
            opacity: 1;
        }
        input:-ms-input-placeholder {
            color: #9ca3af !important;
        }
        
        /* Select dropdown styling */
        select {
            cursor: pointer;
        }
        
        select option {
            background: #2d2d2d !important;
            color: #ffffff !important;
            padding: 12px 16px !important;
        }
        
        select option:hover {
            background: #3d3d3d !important;
        }
        
        select option:checked {
            background: linear-gradient(135deg, #830866 0%, #a10a7f 100%) !important;
            color: #ffffff !important;
        }
        
        /* Add dropdown arrow icon */
        select {
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23ffffff' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 16px center;
            padding-right: 40px !important;
        }

        /* Account type buttons - responsive */
        .register-account-type-wrap {
            margin-bottom: 20px;
        }
        .register-account-type-label {
            display: block;
            font-size: 14px;
            font-weight: 500;
            color: #d1d5db;
            margin-bottom: 12px;
        }
        .register-account-type-label .required-star {
            color: #ff6b6b;
        }
        .register-account-type-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }
        @media (max-width: 480px) {
            .register-account-type-buttons {
                grid-template-columns: 1fr;
                gap: 10px;
            }
        }
        .register-account-type-btn {
            display: flex;
            align-items: center;
            padding: 12px 14px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.25s ease;
            text-align: left;
        }
        .register-account-type-btn:hover {
            border-color: rgba(131, 8, 102, 0.5);
            background: rgba(131, 8, 102, 0.08);
        }
        .register-account-type-btn.selected {
            border-color: #830866;
            background: rgba(131, 8, 102, 0.15);
            box-shadow: 0 0 0 1px rgba(131, 8, 102, 0.3);
        }
        .register-account-type-btn .btn-radio {
            width: 20px;
            height: 20px;
            min-width: 20px;
            border: 2px solid #830866;
            border-radius: 50%;
            margin-right: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .register-account-type-btn .btn-radio .dot {
            width: 8px;
            height: 8px;
            background: #830866;
            border-radius: 50%;
            display: none;
        }
        .register-account-type-btn.selected .btn-radio .dot {
            display: block;
        }
        .register-account-type-btn .btn-text .title {
            font-weight: 600;
            color: #fff;
            font-size: 14px;
            line-height: 1.25;
            margin-bottom: 1px;
        }
        .register-account-type-btn .btn-text .subtitle {
            font-size: 11px;
            color: #9ca3af;
            line-height: 1.25;
        }
        @media (max-width: 480px) {
            .register-account-type-btn {
                padding: 11px 12px;
            }
            .register-account-type-btn .btn-text .title {
                font-size: 13px;
            }
            .register-account-type-btn .btn-text .subtitle {
                font-size: 11px;
            }
        }
    </style>
@stop

@section('content')
<div class="auth-page-container">
    <div style="width: 100%; max-width: 450px;">
        <!-- Logo/Brand -->
        <div class="auth-logo-section">
            <a href="{{route('home')}}" style="display: inline-block;">
                <img src="{{asset( (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo')) : (Cookie::get('app_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo'))) )}}" alt="Logo">
            </a>
            <h1 class="auth-title">Create Account</h1>
            <p class="auth-subtitle">Join us and start creating amazing content</p>
        </div>

        <!-- Register Card -->
        <div class="auth-card">
            @include('auth.register-form')
            @include('auth.social-login-box')
        </div>

        <!-- Footer Links -->
        <div class="auth-footer-links">
            <p>
                Already have an account? 
                <a href="{{route('login')}}">Sign in</a>
            </p>
        </div>
    </div>
</div>
@endsection
