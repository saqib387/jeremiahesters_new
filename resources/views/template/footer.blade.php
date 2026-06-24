<footer class="footer-modern py-5">
    <div class="container">
        <div class="footer-content-wrapper">
            <div class="footer-top-section">
                <div class="d-flex flex-column flex-md-row justify-content-md-between align-items-center">
                    <div class="footer-brand-section">
                        <!-- About -->
                        <div class="headline d-flex">
                            <a href="{{route('home')}}" class="footer-logo-link">
                                <img class="brand-logo footer-logo" src="{{asset( (Cookie::get('app_theme') == null ? (getSetting('site.default_user_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo')) : (Cookie::get('app_theme') == 'dark' ? getSetting('site.dark_logo') : getSetting('site.light_logo'))) )}}" alt="{{__("Site logo")}}">
                            </a>
                        </div>
                        <p class="footer-description-text">{{ getSetting('site.description') ?? 'Premium creators platform for content monetization' }}</p>
                    </div>
                    <div class="d-flex justify-content-md-center align-items-center mt-4 mt-md-0 footer-social-links">
                        @if(getSetting('social-links.facebook_url'))
                            <a class="m-2" href="{{getSetting('social-links.facebook_url')}}" target="_blank" alt="{{__("Facebook")}}" title="{{__("Facebook")}}">
                                @include('elements.icon',['icon'=>'logo-facebook','variant'=>'medium','classes' => 'opacity-8'])
                            </a>
                        @endif
                        @if(getSetting('social-links.twitter_url'))
                            <a class="m-2" href="{{getSetting('social-links.twitter_url')}}" target="_blank" alt="{{__("Twitter")}}" title="{{__("Twitter")}}">
                                @include('elements.icon',['icon'=>'logo-twitter','variant'=>'medium','classes' => 'opacity-8'])
                            </a>
                        @endif
                        @if(getSetting('social-links.instagram_url'))
                            <a class="m-2" href="{{getSetting('social-links.instagram_url')}}" target="_blank" alt="{{__("Instagram")}}" title="{{__("Instagram")}}">
                                @include('elements.icon',['icon'=>'logo-instagram','variant'=>'medium','classes' => 'opacity-8'])
                            </a>
                        @endif
                        @if(getSetting('social-links.whatsapp_url'))
                            <a class="m-2" href="{{getSetting('social-links.whatsapp_url')}}" target="_blank" alt="{{__("Whatsapp")}}" title="{{__("Whatsapp")}}">
                                @include('elements.icon',['icon'=>'logo-whatsapp','variant'=>'medium','classes' => 'opacity-8'])
                            </a>
                        @endif
                        @if(getSetting('social-links.tiktok_url'))
                            <a class="m-2" href="{{getSetting('social-links.tiktok_url')}}" target="_blank" alt="{{__("Tiktok")}}" title="{{__("Tiktok")}}">
                                @include('elements.icon',['icon'=>'logo-tiktok','variant'=>'medium','classes' => 'opacity-8'])
                            </a>
                        @endif
                        @if(getSetting('social-links.youtube_url'))
                            <a class="m-2" href="{{getSetting('social-links.youtube_url')}}" target="_blank" alt="{{__("Youtube")}}" title="{{__("Youtube")}}">
                                @include('elements.icon',['icon'=>'logo-youtube','variant'=>'medium','classes' => 'opacity-8'])
                            </a>
                        @endif
                        @if(getSetting('social-links.telegram_link'))
                            <a class="m-2" href="{{getSetting('social-links.telegram_link')}}" target="_blank" alt="{{__("Telegram")}}" title="{{__("Telegram")}}">
                                @include('elements.icon',['icon'=>'paper-plane','variant'=>'medium','classes' => 'text-lg opacity-8'])
                            </a>
                        @endif
                        @if(getSetting('social-links.reddit_url'))
                            <a class="m-2" href="{{getSetting('social-links.reddit_url')}}" target="_blank" alt="{{__("Reddit")}}" title="{{__("Reddit")}}">
                                @include('elements.icon',['icon'=>'logo-reddit','variant'=>'medium','classes' => 'text-lg opacity-8'])
                            </a>
                        @endif
                    </div>
                </div>

                <div class="footer-links-section">
                    <div class="d-flex flex-column flex-md-row flex-wrap mt-3 mt-md-4 footer-links">
                        <a href="{{route('contact')}}" class="footer-link-item">
                            {{__('Contact')}}
                        </a>
                        @foreach(GenericHelper::getFooterPublicPages() as $page)
                            <a href="{{route('pages.get',['slug' => $page->slug])}}" class="footer-link-item">{{__($page->title)}}</a>
                        @endforeach
                        <a href="{{ route('community.guidelines') }}" class="footer-link-item" target="_blank">
                            {{__('Community Guidelines')}}
                        </a>
                        <a href="{{ route('privacy.policy') }}" class="footer-link-item" target="_blank">
                            {{__('Privacy Policy')}}
                        </a>
                        <a href="{{ route('dmca.index') }}" class="footer-link-item">
                            {{__('DMCA')}}
                        </a>
                        @if(Auth::check())
                            <a href="{{ route('my.settings') }}" class="footer-link-item">{{__('Settings')}}</a>
                        @endif
                    </div>
                </div>
                <hr class="footer-divider">
            </div>
        </div>

        <div class="footer-bottom-section">
            <div class="copyRightInfo d-flex flex-column-reverse flex-md-row d-md-flex justify-content-md-between align-items-center">
                <div class="d-flex align-items-center justify-content-center mt-3 mt-md-0">
                    <p class="mb-0 footer-copyright">&copy; {{date('Y')}} <strong>{{getSetting('site.name')}}</strong>. {{__('All rights reserved.')}}</p>
                </div>
                <div class="d-flex justify-content-center footer-actions-wrapper">
                    @include('elements.footer.dark-mode-switcher')
                    @include('elements.footer.direction-switcher')
                    @include('elements.footer.language-switcher')
                </div>
            </div>
        </div>

    </div>
</footer>

<style>
/* ============================================
   Modern Footer Styles
   ============================================ */
.footer-modern {
    background: linear-gradient(180deg, #000000 0%, #0a0a0a 100%);
    border-top: 1px solid rgba(255, 0, 80, 0.2);
    margin-top: 0;
    position: relative;
    overflow: hidden;
}

/* Prevent Tailwind CSS from overriding footer styles */
.footer-modern * {
    box-sizing: border-box;
}

.footer-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255, 0, 80, 0.5), transparent);
}

.footer-content-wrapper {
    position: relative;
    z-index: 1;
}

.footer-top-section {
    padding-bottom: 2rem;
}

.footer-brand-section {
    max-width: 400px;
}

.footer-logo-link {
    display: inline-block;
    margin-bottom: 1rem;
    transition: transform 0.3s ease;
}

.footer-logo-link:hover {
    transform: scale(1.05);
}

.footer-logo {
    height: 50px;
    width: auto;
    filter: brightness(0) invert(1);
}

.footer-description-text {
    color: rgba(255, 255, 255, 0.7);
    font-size: 14px;
    line-height: 1.6;
    margin: 0;
    max-width: 350px;
}

.footer-social-links {
    display: flex;
    gap: 0.75rem;
    flex-wrap: wrap;
}

.footer-social-links a {
    width: 44px !important;
    height: 44px !important;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.1);
    display: flex;
    align-items: center;
    justify-content: center;
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    transition: all 0.3s ease;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

/* Fix icon sizes inside footer social links - override Tailwind CSS conflicts */
.footer-social-links a .ion-icon-wrapper {
    width: 24px !important;
    height: 24px !important;
    font-size: 24px !important;
    max-width: 24px !important;
    max-height: 24px !important;
    min-width: 24px !important;
    min-height: 24px !important;
}

.footer-social-links a .ion-icon-inner,
.footer-social-links a .ion-icon-inner svg {
    width: 24px !important;
    height: 24px !important;
    max-width: 24px !important;
    max-height: 24px !important;
}

.footer-social-links a:hover {
    background: linear-gradient(135deg, #FF0050 0%, #FF3366 100%);
    color: white;
    border-color: #FF0050;
    transform: translateY(-3px);
    box-shadow: 0 6px 20px rgba(255, 0, 80, 0.3);
}

.footer-links-section {
    margin-top: 2rem;
}

.footer-links {
    gap: 0.5rem;
}

.footer-link-item {
    color: rgba(255, 255, 255, 0.7);
    text-decoration: none;
    font-size: 14px;
    padding: 0.5rem 1rem;
    border-radius: 8px;
    transition: all 0.2s ease;
    display: inline-block;
}

.footer-link-item:hover {
    color: #FF0050;
    background: rgba(255, 0, 80, 0.1);
    transform: translateX(4px);
}

.footer-divider {
    border-color: rgba(255, 255, 255, 0.1);
    margin: 2rem 0 1.5rem;
}

.footer-bottom-section {
    padding-top: 1.5rem;
}

.footer-copyright {
    color: rgba(255, 255, 255, 0.6);
    font-size: 14px;
}

.footer-copyright strong {
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
}

.footer-actions-wrapper {
    gap: 0.5rem;
}

/* Responsive Footer */
@media (max-width: 768px) {
    .footer-modern {
        padding: 3rem 0 2rem;
    }
    
    .footer-brand-section {
        text-align: center;
        max-width: 100%;
        margin-bottom: 2rem;
    }
    
    .footer-description-text {
        max-width: 100%;
        text-align: center;
    }
    
    .footer-social-links {
        justify-content: center;
    }
    
    .footer-social-links a {
        width: 44px !important;
        height: 44px !important;
    }
    
    .footer-social-links a .ion-icon-wrapper {
        width: 24px !important;
        height: 24px !important;
        font-size: 24px !important;
    }
    
    .footer-links {
        justify-content: center;
        text-align: center;
    }
    
    .footer-link-item {
        padding: 0.4rem 0.75rem;
        font-size: 13px;
    }
    
    .footer-copyright {
        text-align: center;
        font-size: 12px;
    }
    
    .footer-actions-wrapper {
        justify-content: center;
        flex-wrap: wrap;
    }
}
</style>
