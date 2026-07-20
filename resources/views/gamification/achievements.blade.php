@extends('layouts.user-no-nav')

@section('page_title', __('Achievements'))

@section('content')
<div class="container py-4" style="max-width:720px;">
    <div style="background:linear-gradient(135deg,#830866 0%,#a10a7f 100%);color:#fff;border-radius:16px;padding:1.5rem;margin-bottom:1.5rem;">
        <h1 style="font-weight:800;font-size:1.6rem;margin:0;">🏅 {{ __('Achievements') }}</h1>
        <p style="opacity:.9;margin:.25rem 0 0;">{{ $unlocked->count() }} / {{ count($all) }} {{ __('unlocked') }}</p>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:1rem;">
        @foreach($all as $key => $a)
            @php($isUnlocked = $unlocked->has($key))
            <div style="background:#fff;border:1px solid rgba(0,0,0,.07);border-radius:16px;padding:1.1rem;text-align:center;box-shadow:0 2px 10px rgba(0,0,0,.04);{{ $isUnlocked ? '' : 'opacity:.45;filter:grayscale(1);' }}">
                <div style="font-size:2.3rem;line-height:1;">{{ $a['icon'] }}</div>
                <div style="font-weight:700;margin-top:.5rem;">{{ __($a['name']) }}</div>
                <div style="font-size:.8rem;color:#718096;margin-top:.25rem;">{{ __($a['desc']) }}</div>
                <div style="font-size:.72rem;font-weight:600;margin-top:.5rem;color:{{ $isUnlocked ? '#1e7e34' : '#830866' }};">
                    @if($isUnlocked)
                        ✓ {{ __('Unlocked') }}
                    @else
                        +{{ $a['xp'] }} XP
                    @endif
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
