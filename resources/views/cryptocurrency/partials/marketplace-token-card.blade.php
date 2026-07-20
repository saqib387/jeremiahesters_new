@php
    $change = (float) ($crypto->price_change_percentage ?? $crypto->price_change_24h ?? 0);
    $isUp = $change >= 0;
    $priceDecimals = $crypto->current_price < 1 ? 4 : 2;
    $creatorAvatar = null;
    if ($crypto->creator) {
        $creatorAvatar = $crypto->creator->avatar ?? asset('img/default-avatar.png');
    }
@endphp
<article class="mp-token">
    <a href="{{ route('cryptocurrency.show', $crypto->id) }}" class="mp-token__body">
        <div class="mp-token__top">
            <div class="mp-token__id">
                @if($crypto->logo)
                    <img src="{{ asset('storage/' . $crypto->logo) }}" alt="{{ $crypto->name }}" class="mp-token__logo">
                @else
                    <span class="mp-token__logo mp-token__logo--fallback">{{ strtoupper(substr($crypto->symbol ?? $crypto->name, 0, 2)) }}</span>
                @endif
                <div class="mp-token__meta">
                    <span class="mp-token__name">{{ $crypto->name }}</span>
                    <span class="mp-token__symbol">{{ $crypto->symbol }}</span>
                </div>
            </div>
            <span class="mp-token__change mp-token__change--{{ $isUp ? 'up' : 'down' }}">
                {{ $isUp ? '+' : '' }}{{ number_format($change, 2) }}%
            </span>
        </div>

        <div class="mp-token__price">${{ number_format($crypto->current_price, $priceDecimals) }}</div>

        <div class="mp-token__stats">
            <div class="mp-token__stat">
                <span class="mp-token__stat-label">{{ __('Market Cap') }}</span>
                <span class="mp-token__stat-value">${{ number_format($crypto->market_cap ?? 0, 0) }}</span>
            </div>
            <div class="mp-token__stat">
                <span class="mp-token__stat-label">{{ __('Supply') }}</span>
                <span class="mp-token__stat-value">{{ number_format($crypto->total_supply ?? 0, 0) }}</span>
            </div>
        </div>

        @if($crypto->creator)
            <div class="mp-token__creator">
                <img src="{{ $creatorAvatar }}" alt="" class="mp-token__creator-avatar" loading="lazy">
                <span>{{ __('by') }} {{ $crypto->creator->name }}</span>
            </div>
        @endif
    </a>

    <div class="mp-token__actions">
        <a href="{{ route('cryptocurrency.buy.form', $crypto->id) }}" class="mp-token__action mp-token__action--buy">{{ __('Buy') }}</a>
        <a href="{{ route('cryptocurrency.show', $crypto->id) }}" class="mp-token__action">{{ __('Details') }}</a>
    </div>
</article>
