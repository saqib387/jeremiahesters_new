@extends('layouts.generic')

@section('page_title', __('Cryptocurrency'))

@section('content')
<div class="crypto-redirect-page">
    <!-- Animated Background Elements -->
    <div class="bg-animation">
        <div class="bg-circle bg-circle-1"></div>
        <div class="bg-circle bg-circle-2"></div>
        <div class="bg-circle bg-circle-3"></div>
    </div>

    <div class="container">
        <div class="crypto-redirect-container">
            <!-- Main Card -->
            <div class="crypto-redirect-card">
                <!-- Icon Section with Animation -->
                <div class="crypto-icon-section">
                    <div class="icon-glow"></div>
                    <div class="crypto-icon-wrapper">
                        <svg width="100" height="100" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="crypto-icon">
                            <circle cx="12" cy="12" r="10" stroke="url(#cryptoGradient)" stroke-width="2"/>
                            <path d="M12 6v12M9 9l3-3 3 3M9 15l3 3 3-3" stroke="url(#cryptoGradient)" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <circle cx="12" cy="8" r="1.5" fill="url(#cryptoGradient)"/>
                            <circle cx="12" cy="16" r="1.5" fill="url(#cryptoGradient)"/>
                            <defs>
                                <linearGradient id="cryptoGradient" x1="0%" y1="0%" x2="100%" y2="100%">
                                    <stop offset="0%" style="stop-color:#F7931A;stop-opacity:1" />
                                    <stop offset="50%" style="stop-color:#FFA500;stop-opacity:1" />
                                    <stop offset="100%" style="stop-color:#FFD700;stop-opacity:1" />
                                </linearGradient>
                            </defs>
                        </svg>
                    </div>
                </div>
                
                <!-- Content Section -->
                <div class="crypto-content-section">
                    <h1 class="crypto-title">
                        <span class="title-text">{{ __('Cryptocurrency') }}</span>
                        <span class="title-underline"></span>
                    </h1>
                    
                    <p class="crypto-description">
                        {{ __('Connect with creators and request custom content through our innovative platform') }}
                    </p>
                    
                    <!-- Features List -->
                    <div class="features-list">
                        <div class="feature-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17l-5-5" stroke="#28a745" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>{{ __('Secure Transactions') }}</span>
                        </div>
                        <div class="feature-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17l-5-5" stroke="#28a745" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>{{ __('Direct Creator Access') }}</span>
                        </div>
                        <div class="feature-item">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 6L9 17l-5-5" stroke="#28a745" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            <span>{{ __('Custom Content Requests') }}</span>
                        </div>
                    </div>
                </div>
                
                <!-- Button Section -->
                <div class="crypto-button-section">
                    <a href="{{ route('custom-requests.marketplace') }}" class="btn-crypto-custom-request">
                        <span class="btn-content">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" class="btn-icon">
                                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="2"/>
                            </svg>
                            <span class="btn-text">{{ __('Create Custom Request') }}</span>
                        </span>
                        <span class="btn-shine"></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* ============================================
   CSS Variables
   ============================================ */
:root {
    --crypto-primary: #F7931A;
    --crypto-primary-light: #FFA500;
    --crypto-primary-gold: #FFD700;
    --crypto-accent: #FF0050;
    --crypto-accent-light: #FF3366;
    --crypto-bg: #0a0e27;
    --crypto-bg-light: #1a1f3a;
    --crypto-text: #ffffff;
    --crypto-text-muted: #a0aec0;
    --crypto-border: rgba(255, 255, 255, 0.1);
    --crypto-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    --crypto-shadow-lg: 0 30px 80px rgba(247, 147, 26, 0.2);
}

/* ============================================
   Page Container
   ============================================ */
.crypto-redirect-page {
    min-height: calc(100vh - 200px);
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 60px 20px;
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, #0a0e27 0%, #1a1f3a 50%, #0f1629 100%);
}

/* ============================================
   Animated Background
   ============================================ */
.bg-animation {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    overflow: hidden;
    z-index: 0;
}

.bg-circle {
    position: absolute;
    border-radius: 50%;
    background: linear-gradient(135deg, rgba(247, 147, 26, 0.1) 0%, rgba(255, 0, 80, 0.1) 100%);
    filter: blur(80px);
    animation: float 20s ease-in-out infinite;
}

.bg-circle-1 {
    width: 400px;
    height: 400px;
    top: -100px;
    left: -100px;
    animation-delay: 0s;
}

.bg-circle-2 {
    width: 300px;
    height: 300px;
    bottom: -50px;
    right: -50px;
    animation-delay: 7s;
}

.bg-circle-3 {
    width: 350px;
    height: 350px;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    animation-delay: 14s;
}

@keyframes float {
    0%, 100% {
        transform: translate(0, 0) scale(1);
        opacity: 0.5;
    }
    33% {
        transform: translate(30px, -30px) scale(1.1);
        opacity: 0.7;
    }
    66% {
        transform: translate(-30px, 30px) scale(0.9);
        opacity: 0.6;
    }
}

/* ============================================
   Main Container
   ============================================ */
.crypto-redirect-container {
    width: 100%;
    max-width: 700px;
    margin: 0 auto;
    position: relative;
    z-index: 1;
}

/* ============================================
   Main Card
   ============================================ */
.crypto-redirect-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: 32px;
    padding: 60px 50px;
    text-align: center;
    border: 1px solid var(--crypto-border);
    box-shadow: var(--crypto-shadow), 0 0 0 1px rgba(255, 255, 255, 0.05) inset;
    position: relative;
    overflow: hidden;
    animation: slideUp 0.8s ease-out;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.crypto-redirect-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 2px;
    background: linear-gradient(90deg, transparent, var(--crypto-primary), var(--crypto-accent), var(--crypto-primary), transparent);
    animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% {
        opacity: 0.5;
        transform: translateX(-100%);
    }
    50% {
        opacity: 1;
        transform: translateX(100%);
    }
}

.crypto-redirect-card:hover {
    transform: translateY(-8px);
    box-shadow: var(--crypto-shadow-lg), 0 0 0 1px rgba(255, 255, 255, 0.1) inset;
    border-color: rgba(247, 147, 26, 0.3);
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(30px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* ============================================
   Icon Section
   ============================================ */
.crypto-icon-section {
    position: relative;
    margin-bottom: 40px;
    display: flex;
    justify-content: center;
    align-items: center;
}

.icon-glow {
    position: absolute;
    width: 150px;
    height: 150px;
    background: radial-gradient(circle, rgba(247, 147, 26, 0.3) 0%, transparent 70%);
    border-radius: 50%;
    animation: pulse 2s ease-in-out infinite;
    z-index: 0;
}

@keyframes pulse {
    0%, 100% {
        transform: scale(1);
        opacity: 0.5;
    }
    50% {
        transform: scale(1.2);
        opacity: 0.8;
    }
}

.crypto-icon-wrapper {
    position: relative;
    z-index: 1;
    animation: iconFloat 3s ease-in-out infinite;
}

@keyframes iconFloat {
    0%, 100% {
        transform: translateY(0) rotate(0deg);
    }
    50% {
        transform: translateY(-10px) rotate(5deg);
    }
}

.crypto-icon {
    filter: drop-shadow(0 10px 30px rgba(247, 147, 26, 0.4));
    animation: iconRotate 20s linear infinite;
}

@keyframes iconRotate {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

/* ============================================
   Content Section
   ============================================ */
.crypto-content-section {
    margin-bottom: 40px;
}

.crypto-title {
    font-size: 48px;
    font-weight: 800;
    margin-bottom: 20px;
    position: relative;
    display: inline-block;
    background: linear-gradient(135deg, #F7931A 0%, #FFA500 50%, #FFD700 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    letter-spacing: -0.02em;
    line-height: 1.2;
}

.title-underline {
    position: absolute;
    bottom: -8px;
    left: 50%;
    transform: translateX(-50%);
    width: 80px;
    height: 4px;
    background: linear-gradient(90deg, transparent, var(--crypto-primary), var(--crypto-accent), var(--crypto-primary), transparent);
    border-radius: 2px;
    animation: underlineExpand 1s ease-out 0.5s both;
}

@keyframes underlineExpand {
    from {
        width: 0;
        opacity: 0;
    }
    to {
        width: 80px;
        opacity: 1;
    }
}

.crypto-description {
    font-size: 18px;
    color: var(--crypto-text-muted);
    margin-bottom: 35px;
    line-height: 1.7;
    max-width: 500px;
    margin-left: auto;
    margin-right: auto;
}

/* ============================================
   Features List
   ============================================ */
.features-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
    margin-bottom: 40px;
    max-width: 400px;
    margin-left: auto;
    margin-right: auto;
}

.feature-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    background: rgba(255, 255, 255, 0.03);
    border-radius: 12px;
    border: 1px solid rgba(255, 255, 255, 0.05);
    transition: all 0.3s ease;
    animation: fadeInUp 0.6s ease-out;
    animation-fill-mode: both;
}

.feature-item:nth-child(1) {
    animation-delay: 0.2s;
}

.feature-item:nth-child(2) {
    animation-delay: 0.4s;
}

.feature-item:nth-child(3) {
    animation-delay: 0.6s;
}

@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.feature-item:hover {
    background: rgba(255, 255, 255, 0.06);
    border-color: rgba(247, 147, 26, 0.2);
    transform: translateX(5px);
}

.feature-item svg {
    flex-shrink: 0;
}

.feature-item span {
    color: var(--crypto-text);
    font-size: 15px;
    font-weight: 500;
}

/* ============================================
   Button Section
   ============================================ */
.crypto-button-section {
    display: flex;
    justify-content: center;
}

.btn-crypto-custom-request {
    display: inline-block;
    position: relative;
    padding: 0;
    border: none;
    background: transparent;
    cursor: pointer;
    overflow: hidden;
    border-radius: 16px;
    text-decoration: none;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.btn-content {
    display: inline-flex;
    align-items: center;
    gap: 14px;
    padding: 20px 50px;
    background: linear-gradient(135deg, #FF0050 0%, #FF3366 50%, #FF0050 100%);
    background-size: 200% 100%;
    color: #ffffff;
    font-size: 18px;
    font-weight: 700;
    border-radius: 16px;
    position: relative;
    z-index: 2;
    transition: all 0.3s ease;
    box-shadow: 0 8px 30px rgba(255, 0, 80, 0.4);
    letter-spacing: 0.3px;
}

.btn-crypto-custom-request:hover .btn-content {
    background-position: 100% 0;
    transform: scale(1.02);
    box-shadow: 0 12px 40px rgba(255, 0, 80, 0.5);
}

.btn-crypto-custom-request:active .btn-content {
    transform: scale(0.98);
}

.btn-shine {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.5s ease;
    z-index: 3;
    border-radius: 16px;
}

.btn-crypto-custom-request:hover .btn-shine {
    left: 100%;
}

.btn-icon {
    width: 22px;
    height: 22px;
    transition: transform 0.3s ease;
}

.btn-crypto-custom-request:hover .btn-icon {
    transform: rotate(90deg) scale(1.1);
}

.btn-text {
    position: relative;
}

/* ============================================
   Responsive Design
   ============================================ */
@media (max-width: 768px) {
    .crypto-redirect-page {
        padding: 40px 15px;
    }
    
    .crypto-redirect-card {
        padding: 40px 30px;
        border-radius: 24px;
    }
    
    .crypto-title {
        font-size: 36px;
    }
    
    .crypto-description {
        font-size: 16px;
    }
    
    .features-list {
        gap: 12px;
    }
    
    .feature-item {
        padding: 10px 16px;
        font-size: 14px;
    }
    
    .btn-content {
        padding: 18px 40px;
        font-size: 16px;
    }
    
    .bg-circle-1,
    .bg-circle-2,
    .bg-circle-3 {
        width: 250px;
        height: 250px;
    }
}

@media (max-width: 480px) {
    .crypto-redirect-card {
        padding: 30px 20px;
    }
    
    .crypto-title {
        font-size: 28px;
    }
    
    .btn-content {
        padding: 16px 32px;
        font-size: 15px;
        gap: 10px;
    }
    
    .btn-icon {
        width: 18px;
        height: 18px;
    }
}
</style>
@endsection
