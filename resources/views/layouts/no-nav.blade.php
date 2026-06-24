<!doctype html>
<html class="h-100" dir="{{GenericHelper::getSiteDirection()}}" lang="{{session('locale')}}">
<head>
    @include('template.head')
</head>
<body style="display: flex; flex-direction: column; min-height: 100vh;">
@include('elements.impersonation-header')
@include('elements.global-announcement')
<div style="flex: 1;">
    @yield('content')
</div>
@if(getSetting('compliance.enable_age_verification_dialog'))
    @include('elements.site-entry-approval-box')
@endif
@include('template.footer-compact',['compact'=>true])
@include('template.jsVars')
@include('template.jsAssets')
@include('elements.language-selector-box')
</body>
</html>
