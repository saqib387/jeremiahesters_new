@php
    $route = $route ?? (Route::currentRouteName() ?? '');
    $isValidated = Auth::check() && GenericHelper::isEmailEnforcedAndValidated();
    $liveStreamsCount = $isValidated && getSetting('streams.allow_streams') ? StreamsHelper::getPublicLiveStreamsCount() : 0;
    $inProgressStream = $isValidated ? StreamsHelper::getUserInProgressStream() : null;
@endphp

<nav class="mobile-sidebar__nav" aria-label="{{ __('Main navigation') }}">
    {{-- Primary --}}
    <div class="mobile-sidebar__group">
        <p class="mobile-sidebar__group-label">{{ __('Menu') }}</p>

        <a href="{{ Auth::check() ? route('feed') : route('home') }}" class="mobile-sidebar__link {{ in_array($route, ['home', 'feed']) ? 'is-active' : '' }}">
            <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'home'])</span>
            <span>{{ __('Home') }}</span>
        </a>

        @auth
            @if($isValidated)
                <a href="{{ route('my.messenger.get') }}" class="mobile-sidebar__link {{ str_starts_with($route, 'my.messenger') ? 'is-active' : '' }}">
                    <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'chat'])</span>
                    <span>{{ __('Messages') }}</span>
                    @if($unreadMessages > 0)
                        <span class="mobile-sidebar__badge">{{ $unreadMessages }}</span>
                    @endif
                </a>

                <a href="{{ route('my.notifications') }}" class="mobile-sidebar__link {{ $route === 'my.notifications' ? 'is-active' : '' }}">
                    <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'notifications'])</span>
                    <span>{{ __('Notifications') }}</span>
                    @if($unreadNotifications > 0)
                        <span class="mobile-sidebar__badge">{{ $unreadNotifications }}</span>
                    @endif
                </a>

                <a href="{{ route('profile', ['username' => Auth::user()->username]) }}" class="mobile-sidebar__link {{ $route === 'profile' && request()->route('username') === Auth::user()->username ? 'is-active' : '' }}">
                    <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'person'])</span>
                    <span>{{ __('My profile') }}</span>
                </a>
            @endif
        @else
            <a href="{{ route('feed') }}" class="mobile-sidebar__link {{ $route === 'feed' ? 'is-active' : '' }}">
                <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'film'])</span>
                <span>{{ __('Feed') }}</span>
            </a>
            <a href="{{ route('custom-requests.marketplace') }}" class="mobile-sidebar__link {{ in_array($route, ['custom-requests.marketplace', 'custom-requests.show', 'custom-requests.my-requests']) ? 'is-active' : '' }}">
                <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'gift'])</span>
                <span>{{ __('Marketplace') }}</span>
            </a>
        @endauth
    </div>

    {{-- Discover --}}
    <div class="mobile-sidebar__group">
        <p class="mobile-sidebar__group-label">{{ __('Discover') }}</p>

        <a href="{{ route('streams.index') }}" class="mobile-sidebar__link {{ $route === 'streams.index' ? 'is-active' : '' }}">
            <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'videocam'])</span>
            <span>{{ __('Livestreams') }}</span>
        </a>

        <a href="{{ route('custom-requests.marketplace') }}" class="mobile-sidebar__link {{ in_array($route, ['custom-requests.marketplace', 'custom-requests.show', 'custom-requests.my-requests']) ? 'is-active' : '' }}">
            <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'gift'])</span>
            <span>{{ __('Custom Requests') }}</span>
        </a>

        @if($isValidated && getSetting('streams.allow_streams'))
            <a href="{{ route('search.get') }}?filter=live" class="mobile-sidebar__link {{ $route === 'search.get' && request()->get('filter') === 'live' ? 'is-active' : '' }}">
                <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'play'])</span>
                <span>{{ __('Streams') }}</span>
                @if($liveStreamsCount > 0)
                    <span class="mobile-sidebar__badge">{{ $liveStreamsCount }}</span>
                @endif
            </a>
        @endif
    </div>

    @if($isValidated)
        {{-- Library & account --}}
        <div class="mobile-sidebar__group">
            <p class="mobile-sidebar__group-label">{{ __('Library') }}</p>

            <a href="{{ route('my.bookmarks') }}" class="mobile-sidebar__link {{ $route === 'my.bookmarks' ? 'is-active' : '' }}">
                <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'bookmark'])</span>
                <span>{{ __('Bookmarks') }}</span>
            </a>

            <a href="{{ route('my.lists.all') }}" class="mobile-sidebar__link {{ in_array($route, ['my.lists.all', 'my.lists.show']) ? 'is-active' : '' }}">
                <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'list'])</span>
                <span>{{ __('Lists') }}</span>
            </a>

            <a href="{{ route('my.settings', ['type' => 'subscriptions']) }}" class="mobile-sidebar__link {{ $route === 'my.settings' && is_int(strpos(Request::path(), 'subscriptions')) ? 'is-active' : '' }}">
                <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'people'])</span>
                <span>{{ __('Subscriptions') }}</span>
            </a>

            <a href="{{ route('cryptocurrency.index') }}" class="mobile-sidebar__link {{ in_array($route, ['cryptocurrency.index', 'cryptocurrency.wallet.index']) ? 'is-active' : '' }}">
                <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'tokens'])</span>
                <span>{{ __('Tokens') }}</span>
            </a>
        </div>

        {{-- More (expandable) --}}
        <div class="mobile-sidebar__group mobile-sidebar__group--more">
            <button type="button" class="mobile-sidebar__link mobile-sidebar__more-toggle" id="mobile-sidebar-more-toggle" aria-expanded="false" aria-controls="mobile-sidebar-more-panel">
                <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'settings'])</span>
                <span>{{ __('More') }}</span>
                <span class="mobile-sidebar__more-chevron">@include('template.partials.mobile-nav-icon', ['icon' => 'chevron'])</span>
            </button>

            <div class="mobile-sidebar__more-panel" id="mobile-sidebar-more-panel" hidden>
                <a href="{{ route('my.settings') }}" class="mobile-sidebar__link mobile-sidebar__link--sub {{ $route === 'my.settings' && !is_int(strpos(Request::path(), 'subscriptions')) ? 'is-active' : '' }}">
                    <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'settings'])</span>
                    <span>{{ __('Settings') }}</span>
                </a>

                <a href="{{ route('pages.get', ['slug' => 'help']) }}" class="mobile-sidebar__link mobile-sidebar__link--sub {{ $route === 'pages.get' && request()->route('slug') === 'help' ? 'is-active' : '' }}">
                    <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'help'])</span>
                    <span>{{ __('Help and support') }}</span>
                </a>

                @include('template.partials.mobile-sidebar-theme-toggle', ['themeToggleSub' => true])

                @if(getSetting('site.allow_language_switch'))
                    <div class="mobile-sidebar__lang-wrap">
                        <button type="button" class="mobile-sidebar__link mobile-sidebar__link--sub mobile-sidebar__lang-toggle" id="mobile-sidebar-lang-toggle" aria-expanded="false">
                            <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'language'])</span>
                            <span>{{ __('Language') }}</span>
                            <span class="mobile-sidebar__more-chevron">@include('template.partials.mobile-nav-icon', ['icon' => 'chevron'])</span>
                        </button>
                        <div class="mobile-sidebar__lang-list" id="mobile-sidebar-lang-list" hidden>
                            @foreach(LocalesHelper::getAvailableLanguages() as $languageCode)
                                @if(LocalesHelper::getLanguageName($languageCode))
                                    <a href="{{ route('language', ['locale' => $languageCode]) }}" class="mobile-sidebar__lang-item">{{ ucfirst(__(LocalesHelper::getLanguageName($languageCode))) }}</a>
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif

                <a href="{{ route('logout') }}" class="mobile-sidebar__link mobile-sidebar__link--sub"
                   onclick="event.preventDefault(); document.getElementById('mobile-sidebar-logout-form').submit();">
                    <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'logout'])</span>
                    <span>{{ __('Log out') }}</span>
                </a>
                <form id="mobile-sidebar-logout-form" action="{{ route('logout') }}" method="POST" class="d-none">@csrf</form>
            </div>
        </div>
    @endif
</nav>

@if($isValidated)
    <div class="mobile-sidebar__actions">
        @if(getSetting('streams.allow_streams') && !getSetting('site.hide_stream_create_menu'))
            <a href="{{ route('my.streams.get') }}{{ $inProgressStream ? '' : (!GenericHelper::isUserVerified() && getSetting('site.enforce_user_identity_checks') ? '' : '?action=create') }}"
               class="mobile-sidebar__action-btn mobile-sidebar__action-btn--live">
                @if($inProgressStream)
                    <span class="mobile-sidebar__live-dot" aria-hidden="true"></span>
                    {{ __('On air') }}
                @else
                    {{ __('Go live') }}
                @endif
            </a>
        @endif

        @if(!getSetting('site.hide_create_post_menu'))
            <a href="{{ route('posts.create') }}" class="mobile-sidebar__action-btn mobile-sidebar__action-btn--primary">
                {{ __('New post') }}
            </a>
        @endif

        <button type="button" class="mobile-sidebar__action-btn mobile-sidebar__action-btn--outline"
                onclick="event.preventDefault(); if (typeof CustomRequest !== 'undefined') CustomRequest.showCreateModal();">
            {{ __('Create Request') }}
        </button>
    </div>
@endif

<div class="mobile-sidebar__footer">
    @if(!Auth::check() || !GenericHelper::isEmailEnforcedAndValidated())
        @include('template.partials.mobile-sidebar-theme-toggle')
    @endif
    @guest
        <a href="{{ route('login') }}" class="mobile-sidebar__link">
            <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'login'])</span>
            <span>{{ __('Login') }}</span>
        </a>
        <button type="button" class="mobile-sidebar__link mobile-sidebar__hide-btn" data-mobile-sidebar-close>
            <span class="mobile-sidebar__icon">@include('template.partials.mobile-nav-icon', ['icon' => 'panel'])</span>
            <span>{{ __('Hide menu') }}</span>
        </button>
    @else
        <a href="{{ route('profile', ['username' => Auth::user()->username]) }}" class="mobile-sidebar__user-card mobile-sidebar__user-card--footer">
            <img src="{{ Auth::user()->avatar }}" alt="" class="mobile-sidebar__user-avatar">
            <div class="mobile-sidebar__user-meta">
                <span class="mobile-sidebar__user-name">{{ Auth::user()->name }}</span>
                <span class="mobile-sidebar__user-handle">{{ '@' . Auth::user()->username }}</span>
            </div>
        </a>
    @endguest
</div>
