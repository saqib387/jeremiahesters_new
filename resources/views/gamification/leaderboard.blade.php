@extends('layouts.user-no-nav')

@section('page_title', __('Leaderboard'))

@section('content')
<div class="container py-4" style="max-width:720px;">
    <div style="background:linear-gradient(135deg,#830866 0%,#a10a7f 100%);color:#fff;border-radius:16px;padding:1.5rem;margin-bottom:1.25rem;">
        <h1 style="font-weight:800;font-size:1.6rem;margin:0;">🏆 {{ __('Leaderboard') }}</h1>
        <p style="opacity:.9;margin:.25rem 0 0;">
            @if($myRank){{ __('Your rank') }}: <strong>#{{ $myRank }}</strong>@else{{ __('Climb the ranks by staying active!') }}@endif
        </p>
    </div>

    <div style="display:flex;gap:8px;margin-bottom:1rem;">
        <a href="{{ route('gamification.leaderboard') }}" class="lb-tab {{ $tab === 'xp' ? 'active' : '' }}">⭐ {{ __('Top XP') }}</a>
        <a href="{{ route('gamification.leaderboard', ['tab' => 'streaks']) }}" class="lb-tab {{ $tab === 'streaks' ? 'active' : '' }}">🔥 {{ __('Top Streaks') }}</a>
    </div>

    <div style="background:#fff;border:1px solid rgba(0,0,0,.07);border-radius:16px;overflow:hidden;box-shadow:0 2px 10px rgba(0,0,0,.04);">
        @forelse($rows as $i => $u)
            <div class="lb-row {{ $me && $u->id === $me->id ? 'me' : '' }}">
                <div class="lb-rank">
                    @if($i === 0) 🥇 @elseif($i === 1) 🥈 @elseif($i === 2) 🥉 @else #{{ $i + 1 }} @endif
                </div>
                <img src="{{ $u->avatar }}" class="lb-avatar" alt="">
                <div class="lb-name">
                    <div style="font-weight:700;" class="text-truncate">{{ $u->name }}</div>
                    <div style="font-size:.78rem;color:#888;" class="text-truncate">{{ '@' . $u->username }} · {{ __('Level') }} {{ $u->level ?? 1 }}</div>
                </div>
                <div class="lb-metric">
                    @if($tab === 'streaks') 🔥 {{ $u->streak_count ?? 0 }} @else {{ number_format($u->xp ?? 0) }} XP @endif
                </div>
            </div>
        @empty
            <div style="padding:2rem;text-align:center;color:#888;">{{ __('No rankings yet — be the first!') }}</div>
        @endforelse
    </div>
</div>

<style>
.lb-tab { flex:1; text-align:center; padding:.65rem; border-radius:12px; background:#fff; border:1px solid rgba(0,0,0,.08); text-decoration:none; color:#555; font-weight:600; }
.lb-tab.active { background:linear-gradient(135deg,#830866,#a10a7f); color:#fff; border-color:transparent; }
.lb-row { display:flex; align-items:center; gap:12px; padding:.75rem 1rem; border-bottom:1px solid rgba(0,0,0,.05); }
.lb-row:last-child { border-bottom:none; }
.lb-row.me { background:rgba(131,8,102,.07); }
.lb-rank { width:34px; text-align:center; font-weight:700; font-size:1.05rem; color:#830866; flex-shrink:0; }
.lb-avatar { width:40px; height:40px; border-radius:50%; object-fit:cover; flex-shrink:0; }
.lb-name { flex:1; min-width:0; }
.lb-metric { font-weight:700; color:#830866; white-space:nowrap; }
</style>
@endsection
