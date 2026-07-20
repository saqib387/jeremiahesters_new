<!doctype html>
<html class="h-100" dir="{{GenericHelper::getSiteDirection()}}" lang="{{session('locale')}}">
<head>
    @include('template.head',['additionalCss' => [
                '/libs/animate.css/animate.css',
                '/libs/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.css',
                '/css/side-menu.css',
                '/css/upload.css',
             ]])
</head>
<body class="d-flex flex-column">
@include('elements.impersonation-header')
@include('elements.global-announcement')
<div class="flex-fill">
    {{-- Mobile top navigation bar (replaces the old bottom bar) --}}
    <div class="d-block d-md-none fixed-top mobile-top-nav-wrapper">
        @include('elements.mobile-navbar')
    </div>
    <style>
        @media (max-width: 767.98px) {
            body { padding-top: 58px; }
        }
    </style>
    @include('template.user-side-menu')

    <div class="container-xl overflow-x-hidden-m">
        <div class="row main-wrapper">
            <div class="col-2 col-md-3 pt-4 p-0 d-none d-md-block">
                @include('template.side-menu')
            </div>
            <div class="col-12 col-md-9 {{(!in_array(Route::currentRouteName(),['my.messenger.get']) ? 'min-vh-100' : '' )}}  border-left px-0 overflow-x-hidden-m content-wrapper {{(in_array(Route::currentRouteName(),['feed','profile','my.messenger.get','search.get','my.notifications','my.bookmarks','my.lists.all','my.lists.show','my.settings','posts.get']) ? '' : 'border-right' )}}">
                @yield('content')
            </div>
        </div>
    </div>

</div>
@if(getSetting('compliance.enable_age_verification_dialog'))
    @include('elements.site-entry-approval-box')
@endif
@include('template.footer-compact',['compact'=>true])
@include('template.jsVars')
<!-- Add utility fix script -->
<script src="{{ asset('js/util-fix.js') }}"></script>
@include('template.jsAssets',['additionalJs' => [
               '/libs/jquery-backstretch/jquery.backstretch.min.js',
               '/libs/wow.js/dist/wow.min.js',
               '/libs/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js',
               '/js/SideMenu.js'
]])
@include('elements.language-selector-box')
@auth
    @include('elements.custom-request.create-modal')
    <script src="{{ asset('js/CustomRequest.js') }}"></script>
@endauth
@include('elements.gamification-celebrations')
</body>
</html>
