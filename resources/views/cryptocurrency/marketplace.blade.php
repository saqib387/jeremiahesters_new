@extends('layouts.generic')

@php
    $isDarkTheme = Cookie::get('app_theme') == null
        ? getSetting('site.default_user_theme') == 'dark'
        : Cookie::get('app_theme') == 'dark';

    $currentSort = request('sort');
    $currentOrder = request('order');
@endphp

@section('styles')
    <link rel="stylesheet" href="{{ asset('css/pages/marketplace.css') }}?v=20260713c">
@endsection

@section('content')
<div class="mp-page {{ $isDarkTheme ? 'mp-page--dark' : 'mp-page--light' }}">
<div class="mp-container">

    <header class="mp-header">
        <h1 class="mp-header__title">{{ __('Token Marketplace') }}</h1>
        <p class="mp-header__sub">{{ __('Discover and invest in creator tokens') }}</p>
    </header>

    <section class="mp-toolbar">
        <form action="{{ route('cryptocurrency.marketplace') }}" method="GET" class="mp-search" role="search">
            @if(request('sort'))
                <input type="hidden" name="sort" value="{{ request('sort') }}">
            @endif
            @if(request('order'))
                <input type="hidden" name="order" value="{{ request('order') }}">
            @endif
            <span class="mp-search__icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none">
                    <circle cx="11" cy="11" r="7" stroke="currentColor" stroke-width="1.75"/>
                    <path d="m20 20-3.5-3.5" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
                </svg>
            </span>
            <input
                id="marketplace-token-search"
                class="mp-search__input"
                type="text"
                name="search"
                placeholder="{{ __('Search tokens by name or symbol...') }}"
                value="{{ request('search') }}"
                autocomplete="off"
                aria-label="{{ __('Search tokens') }}"
            >
            <button type="submit" class="mp-search__btn">{{ __('Search') }}</button>
        </form>

        <div class="mp-filters" role="list">
            <a href="{{ route('cryptocurrency.marketplace', array_filter(['search' => request('search')])) }}"
               class="mp-filter {{ !$currentSort ? 'is-active' : '' }}">{{ __('All') }}</a>
            <a href="{{ route('cryptocurrency.marketplace', array_filter(['search' => request('search'), 'sort' => 'current_price', 'order' => 'desc'])) }}"
               class="mp-filter {{ $currentSort === 'current_price' && $currentOrder === 'desc' ? 'is-active' : '' }}">{{ __('Price: High') }}</a>
            <a href="{{ route('cryptocurrency.marketplace', array_filter(['search' => request('search'), 'sort' => 'current_price', 'order' => 'asc'])) }}"
               class="mp-filter {{ $currentSort === 'current_price' && $currentOrder === 'asc' ? 'is-active' : '' }}">{{ __('Price: Low') }}</a>
            <a href="{{ route('cryptocurrency.marketplace', array_filter(['search' => request('search'), 'sort' => 'market_cap', 'order' => 'desc'])) }}"
               class="mp-filter {{ $currentSort === 'market_cap' ? 'is-active' : '' }}">{{ __('Market Cap') }}</a>
            <a href="{{ route('cryptocurrency.marketplace', array_filter(['search' => request('search'), 'sort' => 'created_at', 'order' => 'desc'])) }}"
               class="mp-filter {{ $currentSort === 'created_at' ? 'is-active' : '' }}">{{ __('Newest') }}</a>
        </div>
    </section>

    @if($trending->count() > 0 && !request('search'))
    <section class="mp-section">
        <div class="mp-section__head">
            <h2 class="mp-section__title">{{ __('Trending Tokens') }}</h2>
        </div>
        <div class="mp-grid">
            @foreach($trending->take(3) as $crypto)
                @include('cryptocurrency.partials.marketplace-token-card', ['crypto' => $crypto])
            @endforeach
        </div>
    </section>
    @endif

    <section class="mp-section">
        <div class="mp-section__head">
            <h2 class="mp-section__title">{{ __('All Tokens') }}</h2>
            <span class="mp-section__count">{{ $cryptocurrencies->total() }} {{ Str::plural('token', $cryptocurrencies->total()) }}</span>
        </div>

        @if($cryptocurrencies->count() > 0)
            <div class="mp-grid">
                @foreach($cryptocurrencies as $crypto)
                    @include('cryptocurrency.partials.marketplace-token-card', ['crypto' => $crypto])
                @endforeach
            </div>

            @if($cryptocurrencies->hasPages())
                <div class="mp-pagination">
                    {{ $cryptocurrencies->links() }}
                </div>
            @endif
        @else
            <div class="mp-empty">
                <span class="mp-empty__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24" fill="none">
                        <circle cx="12" cy="12" r="9" stroke="currentColor" stroke-width="1.75"/>
                        <path d="M12 8v4m0 4h.01" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
                    </svg>
                </span>
                <h3>{{ __('No tokens found') }}</h3>
                <p>{{ request('search') ? __('Try a different search term') : __('Be the first to create a token!') }}</p>
                @if(request('search'))
                    <a href="{{ route('cryptocurrency.marketplace') }}" class="mp-btn mp-btn--ghost">{{ __('Clear search') }}</a>
                @endif
            </div>
        @endif
    </section>

    <section class="mp-cta">
        <div class="mp-cta__text">
            <h2>{{ __('Create Your Own Token') }}</h2>
            <p>{{ __('Launch your cryptocurrency and let your fans invest in your success') }}</p>
        </div>
        <a href="{{ route('cryptocurrency.create') }}" class="mp-btn mp-btn--primary">
            <svg viewBox="0 0 24 24" fill="none" aria-hidden="true">
                <path d="M12 5v14M5 12h14" stroke="currentColor" stroke-width="1.75" stroke-linecap="round"/>
            </svg>
            {{ __('Get Started') }}
        </a>
    </section>

</div>
</div>
@endsection
