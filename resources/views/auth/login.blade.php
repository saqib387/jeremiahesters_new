@extends('layouts.no-nav')
@section('page_title', __('Login'))

@section('page_description', getSetting('site.description'))
@section('share_url', route('home'))
@section('share_title', getSetting('site.name') . ' - ' .  __('Login'))
@section('share_description', getSetting('site.description'))
@section('share_type', 'article')
@section('share_img', GenericHelper::getOGMetaImage())

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
            <h1 class="auth-title">Welcome Back</h1>
            <p class="auth-subtitle">Sign in to continue to your account</p>
        </div>

        <!-- Login Card -->
        <div class="auth-card">
            @include('auth.login-form')
            @include('auth.social-login-box')
        </div>

        <!-- Footer Links -->
        <div class="auth-footer-links">
            <p>
                Don't have an account? 
                <a href="{{route('register')}}">Sign up</a>
            </p>
        </div>
    </div>
</div>
@endsection
