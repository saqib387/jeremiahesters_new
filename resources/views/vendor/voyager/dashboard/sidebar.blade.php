<div class="side-menu sidebar-inverse">
    <nav class="navbar navbar-default" role="navigation">
        <div class="side-menu-container">
            <div class="navbar-header">
                <a class="navbar-brand" href="{{ route('voyager.dashboard') }}">
                    <div class="logo-icon-container">
                        <?php $admin_logo_img = Voyager::setting('admin.icon_image', ''); ?>
                        @if ($admin_logo_img == '')
                            <img src="{{ asset('/img/rounded-logo-white.svg') }}" alt="Logo Icon">
                        @else
                            <img src="{{ Voyager::image($admin_logo_img) }}" alt="Logo Icon">
                        @endif
                    </div>
                    <div class="title">{{ Voyager::setting('admin.title', 'VOYAGER') }}</div>
                </a>
            </div><!-- .navbar-header -->

            <div class="admin-profile-card">
                <img src="{{ $user_avatar }}" class="avatar" alt="{{ Auth::user()->name }} avatar">
                <div class="admin-profile-meta">
                    <strong>{{ ucwords(Auth::user()->name) }}</strong>
                    <span>{{ Auth::user()->email }}</span>
                </div>
            </div>
        </div>

        @php
            $fallbackRoutes = [
                'Dashboard' => 'voyager.dashboard',
                'Tax Revenue' => 'voyager.revenue.index',
                'Token Earnings' => 'voyager.tokens.index',
                'Wallet Activity' => 'voyager.wallets.index',
                'Creator Tokens' => 'voyager.tokens.index',
                'Requests & Approvals' => 'voyager.user-verifies.index',
            ];

            $fallbackNav = [
                ['title' => 'Dashboard', 'icon' => 'voyager-dashboard', 'route' => 'voyager.dashboard', 'active' => ['voyager.dashboard']],
                ['title' => 'Tokens', 'icon' => 'voyager-trophy', 'route' => 'voyager.tokens.index', 'active' => ['voyager.tokens.*']],
                ['title' => 'Wallets', 'icon' => 'voyager-wallet', 'route' => 'voyager.wallets.index', 'active' => ['voyager.wallets.*']],
                ['title' => 'Revenue', 'icon' => 'voyager-dollar', 'route' => 'voyager.revenue.index', 'active' => ['voyager.revenue.*']],
                ['title' => 'Users', 'icon' => 'voyager-people', 'route' => 'voyager.users.index', 'active' => ['voyager.users.*']],
                ['title' => 'Media', 'icon' => 'voyager-images', 'route' => 'voyager.media.index', 'active' => ['voyager.media.*']],
                ['title' => 'Settings', 'icon' => 'voyager-settings', 'route' => 'voyager.settings.index', 'active' => ['voyager.settings.*']],
            ];

            $renderedHrefs = [];
            $currentUrl = url()->current();
            $normalizeUrl = function ($url) {
                $path = parse_url($url, PHP_URL_PATH) ?: '/';
                $path = Str::start($path, '/');
                return rtrim($path, '/') ?: '/';
            };
        @endphp

        <div id="adminmenu">
            <ul class="admin-sidebar-nav">
                @foreach ($fallbackNav as $item)
                    @continue(!Route::has($item['route']))
                    @php
                        $href = route($item['route']);
                        $path = $normalizeUrl($href);
                        $active = request()->routeIs(...$item['active']) || $normalizeUrl($currentUrl) === $path;
                        $renderedHrefs[] = $path;
                    @endphp
                    <li>
                        <a href="{{ $href }}" class="{{ $active ? 'active' : '' }}">
                            <i class="{{ $item['icon'] }}"></i>
                            <span>{{ $item['title'] }}</span>
                        </a>
                    </li>
                @endforeach

                @foreach (menu('admin', '_json') as $menuItem)
                    @php
                        $routeName = $fallbackRoutes[$menuItem->title] ?? $menuItem->route;
                        $href = ($routeName && Route::has($routeName)) ? route($routeName) : ($menuItem->href ?? '#');
                        $hasUsableHref = $href && $href !== '#';
                        $path = $hasUsableHref ? $normalizeUrl($href) : null;
                        $active = $hasUsableHref && $normalizeUrl($currentUrl) === $path;
                    @endphp

                    @continue(!$hasUsableHref || in_array($path, $renderedHrefs, true))

                    @php
                        $renderedHrefs[] = $path;
                    @endphp

                    <li>
                        <a href="{{ $href }}" class="{{ $active ? 'active' : '' }}">
                            <i class="{{ $menuItem->icon_class ?: 'voyager-dot' }}"></i>
                            <span>{{ $menuItem->title }}</span>
                        </a>

                        @if($menuItem->children && $menuItem->children->count())
                            <ul class="admin-sidebar-subnav">
                                @foreach($menuItem->children as $child)
                                    @php
                                        $childHref = $child->href ?? '#';
                                        $childActive = $childHref && $childHref !== '#' && $normalizeUrl($currentUrl) === $normalizeUrl($childHref);
                                    @endphp
                                    @continue(!$childHref || $childHref === '#')
                                    <li>
                                        <a href="{{ $childHref }}" class="{{ $childActive ? 'active' : '' }}">
                                            <i class="{{ $child->icon_class ?: 'voyager-dot' }}"></i>
                                            <span>{{ $child->title }}</span>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    </nav>
</div>
