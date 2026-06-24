@if(getSetting('social-login.facebook_client_id') || getSetting('social-login.twitter_client_id') || getSetting('social-login.google_client_id') || true)
    <div style="margin-top: 24px;">
        <div style="position: relative; margin: 20px 0;">
            <div style="position: absolute; top: 50%; left: 0; right: 0; height: 1px; background: rgba(255, 255, 255, 0.2);"></div>
            <div style="position: relative; text-align: center;">
                <span style="background: rgba(255, 255, 255, 0.1); padding: 0 16px; color: #9ca3af; font-size: 14px;">{{__("Or continue with")}}</span>
            </div>
        </div>

        <div style="margin-top: 24px;">
            @if(getSetting('social-login.facebook_client_id'))
                <a href="{{url('',['socialAuth','facebook'])}}" rel="nofollow" style="display: flex; align-items: center; justify-content: center; width: 100%; padding: 12px 16px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; color: #ffffff; text-decoration: none; margin-bottom: 12px; transition: all 0.2s ease; font-weight: 500; font-size: 14px;"
                   onmouseover="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.transform='scale(1.02)';"
                   onmouseout="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.transform='scale(1)';">
                    <img src="{{asset('/img/logos/facebook-logo.svg')}}" style="width: 20px; height: 20px; margin-right: 12px;" alt="Facebook"/>
                    <span>{{__("Sign in with")}} {{__("Facebook")}}</span>
                </a>
            @endif

            @if(getSetting('social-login.twitter_client_id'))
                <a href="{{url('',['socialAuth','twitter'])}}" rel="nofollow" style="display: flex; align-items: center; justify-content: center; width: 100%; padding: 12px 16px; background: rgba(255, 255, 255, 0.1); border: 1px solid rgba(255, 255, 255, 0.2); border-radius: 12px; color: #ffffff; text-decoration: none; margin-bottom: 12px; transition: all 0.2s ease; font-weight: 500; font-size: 14px;"
                   onmouseover="this.style.background='rgba(255, 255, 255, 0.2)'; this.style.transform='scale(1.02)';"
                   onmouseout="this.style.background='rgba(255, 255, 255, 0.1)'; this.style.transform='scale(1)';">
                    <img src="{{asset('/img/logos/twitter-logo.svg')}}" style="width: 20px; height: 20px; margin-right: 12px;" alt="Twitter"/>
                    <span>{{__("Sign in with")}} {{__("Twitter")}}</span>
                </a>
            @endif

            {{-- Google Login - Always visible --}}
            <a href="{{route('social.login.start', 'google')}}" rel="nofollow" style="display: flex; align-items: center; justify-content: center; width: 100%; padding: 12px 16px; background: #ffffff; border: 1px solid #d1d5db; border-radius: 12px; color: #374151; text-decoration: none; margin-bottom: 12px; transition: all 0.2s ease; font-weight: 600; font-size: 14px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);"
               onmouseover="this.style.background='#f9fafb'; this.style.transform='scale(1.02)'; this.style.boxShadow='0 4px 6px rgba(0, 0, 0, 0.1)';"
               onmouseout="this.style.background='#ffffff'; this.style.transform='scale(1)'; this.style.boxShadow='0 1px 3px rgba(0, 0, 0, 0.1)';">
                <svg style="width: 20px; height: 20px; margin-right: 12px;" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z" fill="#4285F4"/>
                    <path d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z" fill="#34A853"/>
                    <path d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z" fill="#FBBC05"/>
                    <path d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z" fill="#EA4335"/>
                </svg>
                <span>{{__("Sign in with")}} {{__("Google")}}</span>
            </a>
        </div>
    </div>
@endif
