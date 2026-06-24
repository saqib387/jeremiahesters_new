<!-- Collapsible Menu -->
<div class="collapse d-lg-block settings-nav" id="settingsNav">
    <div class="card-settings border-bottom">
        <div class="list-group list-group-sm list-group-flush">
            <!-- Creator Dashboard Link -->
            <a href="{{ route('creator.dashboard') }}" class="list-group-item list-group-item-action d-flex justify-content-between" style="background: linear-gradient(135deg, rgba(255,0,80,0.1) 0%, rgba(255,51,102,0.05) 100%); border-left: 3px solid #FF0050;">
                <div class="d-flex align-items-center">
                    @include('elements.icon', ['icon' => 'analytics-outline', 'centered' => 'false', 'classes' => 'mr-3', 'variant' => 'medium'])
                    <span style="font-weight: 600; color: #FF0050;">Creator Dashboard</span>
                </div>
                <div class="d-flex align-items-center">
                    @include('elements.icon', ['icon' => 'chevron-forward-outline'])
                </div>
            </a>
            
            @foreach($availableSettings as $route => $setting)
                <a href="{{ route('my.settings', ['type' => $route]) }}" class="list-group-item list-group-item-action d-flex justify-content-between {{ $activeSettingsTab == $route ? 'active' : '' }}">
                    <div class="d-flex align-items-center">
                        @include('elements.icon', ['icon' => $setting['icon'].'-outline', 'centered' => 'false', 'classes' => 'mr-3', 'variant' => 'medium'])
                        <span>{{ ucfirst(__($route)) }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        @include('elements.icon', ['icon' => 'chevron-forward-outline'])
                    </div>
                </a>
            @endforeach
            
            <!-- Legal & Compliance Section -->
            <div class="list-group-item list-group-item-header text-muted small py-2 px-3" style="background: rgba(0,0,0,0.02); border-top: 1px solid rgba(0,0,0,0.1);">
                <strong>{{ __('Legal & Compliance') }}</strong>
            </div>
            <a href="{{ route('dmca.index') }}" class="list-group-item list-group-item-action d-flex justify-content-between">
                <div class="d-flex align-items-center">
                    @include('elements.icon', ['icon' => 'shield-checkmark-outline', 'centered' => 'false', 'classes' => 'mr-3', 'variant' => 'medium'])
                    <span>{{ __('DMCA & Copyright') }}</span>
                </div>
                <div class="d-flex align-items-center">
                    @include('elements.icon', ['icon' => 'chevron-forward-outline'])
                </div>
            </a>
        </div>
    </div>
</div>
