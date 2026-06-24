<div class="btn-group" style="margin-bottom: 20px;">
    <a href="{{ route('voyager.dashboard') }}" class="btn btn-default">
        <i class="voyager-dashboard"></i> Overview
    </a>
    <a href="{{ route('voyager.dashboard.realtime-stats') }}" class="btn btn-{{ Route::currentRouteName() === 'voyager.dashboard.realtime-stats' ? 'primary' : 'default' }}">
        <i class="voyager-activity"></i> Realtime Stats
    </a>
    <a href="{{ route('voyager.dashboard.chart-data') }}" class="btn btn-{{ Route::currentRouteName() === 'voyager.dashboard.chart-data' ? 'primary' : 'default' }}">
        <i class="voyager-bar-chart"></i> Chart Data
    </a>
    <a href="{{ route('voyager.dashboard.top-performers') }}" class="btn btn-{{ Route::currentRouteName() === 'voyager.dashboard.top-performers' ? 'primary' : 'default' }}">
        <i class="voyager-trophy"></i> Top Performers
    </a>
    <a href="{{ route('voyager.dashboard.system-health') }}" class="btn btn-{{ Route::currentRouteName() === 'voyager.dashboard.system-health' ? 'primary' : 'default' }}">
        <i class="voyager-tools"></i> System Health
    </a>
</div>
