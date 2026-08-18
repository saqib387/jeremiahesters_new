@php
    $me = Auth::user();

    // Referral link target follows the admin's configured landing page.
    $refLink = match (getSetting('referrals.referrals_default_link_page')) {
        'home' => route('home', ['ref' => $me->referral_code]),
        'register' => route('register', ['ref' => $me->referral_code]),
        default => route('profile', ['ref' => $me->referral_code, 'username' => $me->username]),
    };

    // getTotalAmountEarnedFromRewardsByUsers() requires BOTH the referred and referral
    // ids, so the all-referrals total is summed directly rather than calling it with
    // one argument (which throws ArgumentCountError).
    $totalEarned = \App\Model\Reward::where('to_user_id', $me->id)
        ->where('reward_type', \App\Model\Reward::FEE_PERCENTAGE_REWARD_TYPE)
        ->sum('amount');
@endphp

<div class="profile-settings ps">

    {{-- Your link ---------------------------------------------------------------- --}}
    <div class="ps-card">
        <div class="ps-card__head">
            <h2 class="ps-card__title">{{ __('Your referral link') }}</h2>
            <p class="ps-card__sub">{{ __('Share it. When someone signs up through it, you earn a share of what they make.') }}</p>
        </div>

        {{-- #copy-input / #copy-button are the hooks referrals.js binds to — keep both. --}}
        <div class="ref-copy">
            <input type="text" id="copy-input" class="ref-copy__input" readonly
                   value="{{ $refLink }}" aria-label="{{ __('Your referral link') }}">
            <button class="ref-copy__btn" type="button" id="copy-button"
                    data-toggle="tooltip" data-placement="bottom" title="{{ __('Copy to Clipboard') }}">
                {{ __('Copy') }}
            </button>
        </div>
    </div>

    {{-- Stats -------------------------------------------------------------------- --}}
    <div class="ps-card">
        <div class="ps-card__head">
            <h2 class="ps-card__title">{{ __('Your referrals') }}</h2>
        </div>
        <div class="ref-stats">
            <div class="ref-stat">
                <span class="ref-stat__value">{{ number_format($referrals->total()) }}</span>
                <span class="ref-stat__label">{{ __('People referred') }}</span>
            </div>
            <div class="ref-stat">
                <span class="ref-stat__value">{{ \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount($totalEarned) }}</span>
                <span class="ref-stat__label">{{ __('Total earned') }}</span>
            </div>
        </div>
    </div>

    {{-- List --------------------------------------------------------------------- --}}
    <div class="ps-card">
        <div class="ps-card__head">
            <h2 class="ps-card__title">{{ __('Referral list') }}</h2>
        </div>

        @if(count($referrals))
            <ul class="ref-list">
                @foreach($referrals as $referral)
                    @php $u = $referral->usedBy; @endphp
                    <li class="ref-item">
                        {{-- Guard on $u: the old markup called $referral->usedBy->username inside
                             the branch that ran when usedBy was null, which would fatal. --}}
                        @if($u)
                            <a href="{{ route('profile', ['username' => $u->username]) }}" class="ref-item__avatar-link">
                                <img class="ref-item__avatar" src="{{ $u->avatar }}" alt="{{ $u->username }}" loading="lazy">
                            </a>
                        @else
                            <span class="ref-item__avatar-link">
                                <img class="ref-item__avatar" src="{{ \App\Providers\GenericHelperServiceProvider::getStorageAvatarPath(null) }}" alt="" loading="lazy">
                            </span>
                        @endif

                        <span class="ref-item__meta">
                            <span class="ref-item__name">
                                @if($u)
                                    <a href="{{ route('profile', ['username' => $u->username]) }}">{{ $u->name }}</a>
                                @else
                                    {{ __('Deleted user') }}
                                @endif
                            </span>
                            <span class="ref-item__since">
                                {{ __('Since') }} {{ \Carbon\Carbon::parse($referral->created_at)->format('M j, Y') }}
                            </span>
                        </span>

                        <span class="ref-item__earned">
                            {{ \App\Providers\SettingsServiceProvider::getWebsiteFormattedAmount(
                                \App\Providers\UsersServiceProvider::getTotalAmountEarnedFromRewardsByUsers($me->id, $referral->used_by)
                            ) }}
                        </span>
                    </li>
                @endforeach
            </ul>

            @if($referrals->hasPages())
                <div class="ref-pagination">{{ $referrals->onEachSide(1)->links() }}</div>
            @endif
        @else
            <div class="ref-empty">
                <span class="ref-empty__icon" aria-hidden="true">
                    <svg viewBox="0 0 24 24"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M22 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                </span>
                <h3>{{ __('No referrals yet') }}</h3>
                <p>{{ __('Share your link above — you earn from everyone who joins through it.') }}</p>
            </div>
        @endif
    </div>
</div>
