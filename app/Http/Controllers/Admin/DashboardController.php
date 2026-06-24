<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dashboard;
use App\Models\Cryptocurrency;
use App\Models\Wallet;
use App\Models\Revenue;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the main dashboard
     */
    public function index()
    {
        try {
            // Get all dashboard data
            $overviewStats = Dashboard::getOverviewStats();
            $recentActivity = Dashboard::getRecentActivity();
            $alerts = Dashboard::getPlatformAlerts();
            
            // Get quick stats for cards
            $quickStats = [
                'total_platform_value' => $overviewStats['total_market_cap'] + $overviewStats['total_wallet_balance_usd'],
                'monthly_revenue' => Revenue::where('created_at', '>=', now()->subMonth())->sum('revenue_amount'),
                'active_users_percentage' => $this->getActiveUsersPercentage(),
                'distribution_efficiency' => $this->getDistributionEfficiency(),
            ];

            return view('admin.dashboard.index', compact(
                'overviewStats', 
                'recentActivity', 
                'alerts', 
                'quickStats'
            ));
            
        } catch (\Exception $e) {
            // If there's an error, return basic dashboard with error message
            return view('admin.dashboard.index', [
                'overviewStats' => $this->getBasicStats(),
                'recentActivity' => [],
                'alerts' => [[
                    'type' => 'danger',
                    'title' => 'Dashboard Error',
                    'message' => 'Some dashboard data could not be loaded. Please check your database connections.',
                    'action_url' => '#',
                    'action_text' => 'Refresh'
                ]],
                'quickStats' => []
            ]);
        }
    }

    /**
     * Get real-time statistics for AJAX updates
     */
    public function getRealtimeStats(Request $request)
    {
        if ($this->shouldShowDashboardPage($request)) {
            try {
                return view('admin.dashboard.realtime-stats', [
                    'stats' => $this->buildRealtimeStats(),
                    'error' => null,
                ]);
            } catch (\Exception $e) {
                return view('admin.dashboard.realtime-stats', [
                    'stats' => [],
                    'error' => 'Failed to load real-time stats.',
                ]);
            }
        }

        try {
            return response()->json($this->buildRealtimeStats());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch real-time stats'], 500);
        }
    }

    /**
     * Get chart data for dashboard widgets
     */
    public function getChartData(Request $request)
    {
        if ($this->shouldShowDashboardPage($request)) {
            try {
                $days = $this->normalizeChartDays($request->get('days', 30));

                return view('admin.dashboard.chart-data', [
                    'days' => $days,
                    'charts' => $this->buildAllChartData($days),
                    'error' => null,
                ]);
            } catch (\Exception $e) {
                return view('admin.dashboard.chart-data', [
                    'days' => $this->normalizeChartDays($request->get('days', 30)),
                    'charts' => [],
                    'error' => 'Failed to load chart data.',
                ]);
            }
        }

        try {
            $chartType = $request->get('type', 'revenue');
            $days = $this->normalizeChartDays($request->get('days', 30));
            $data = $this->buildChartDataByType($chartType, $days);

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch chart data'], 500);
        }
    }

    /**
     * Get top performers data
     */
    public function getTopPerformers(Request $request)
    {
        if ($this->shouldShowDashboardPage($request)) {
            try {
                return view('admin.dashboard.top-performers', [
                    'data' => $this->buildTopPerformersData(),
                    'error' => null,
                ]);
            } catch (\Exception $e) {
                return view('admin.dashboard.top-performers', [
                    'data' => [],
                    'error' => 'Failed to load top performers.',
                ]);
            }
        }

        try {
            return response()->json($this->buildTopPerformersData());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to fetch top performers'], 500);
        }
    }

    /**
     * Get system health check
     */
    public function getSystemHealth(Request $request)
    {
        if ($this->shouldShowDashboardPage($request)) {
            try {
                return view('admin.dashboard.system-health', [
                    'health' => $this->buildSystemHealthData(),
                    'error' => null,
                ]);
            } catch (\Exception $e) {
                return view('admin.dashboard.system-health', [
                    'health' => [],
                    'error' => 'Failed to load system health.',
                ]);
            }
        }

        try {
            return response()->json($this->buildSystemHealthData());
        } catch (\Exception $e) {
            return response()->json(['error' => 'Health check failed'], 500);
        }
    }

    /**
     * Export dashboard summary
     */
    public function exportSummary(Request $request)
    {
        try {
            $format = $request->get('format', 'csv');
            $overviewStats = Dashboard::getOverviewStats();
            
            $data = [
                ['Metric', 'Value'],
                ['Total Users', $overviewStats['total_users']],
                ['Total Tokens', $overviewStats['total_tokens']],
                ['Active Tokens', $overviewStats['active_tokens']],
                ['Total Wallets', $overviewStats['total_wallets']],
                ['Active Wallets', $overviewStats['active_wallets']],
                ['Total Market Cap', '$' . number_format($overviewStats['total_market_cap'], 2)],
                ['Total Revenue Shares', $overviewStats['total_revenue_shares']],
                ['Pending Distributions', $overviewStats['pending_distributions']],
                ['Overdue Distributions', $overviewStats['overdue_distributions']],
                ['Platform Health Score', $overviewStats['platform_health_score'] . '%'],
                ['Export Date', now()->format('Y-m-d H:i:s')],
            ];

            $filename = 'dashboard_summary_' . date('Y-m-d_H-i-s') . '.csv';
            
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function() use ($data) {
                $file = fopen('php://output', 'w');
                foreach ($data as $row) {
                    fputcsv($file, $row);
                }
                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to export dashboard summary');
        }
    }

    // Helper methods

    protected function shouldShowDashboardPage(Request $request)
    {
        return $request->acceptsHtml()
            && ! $request->ajax()
            && ! $request->expectsJson();
    }

    protected function buildRealtimeStats()
    {
        $overviewStats = Dashboard::getOverviewStats();

        return [
            'pending_distributions' => Revenue::pending()->count(),
            'overdue_distributions' => Revenue::pending()->where('created_at', '<', now()->subDays(30))->count(),
            'active_wallets' => Wallet::active()->count(),
            'total_wallet_balance_usd' => $this->getTotalWalletBalanceUsd(),
            'platform_health_score' => $overviewStats['platform_health_score'],
            'new_users_today' => $overviewStats['new_users_today'],
            'last_updated' => now()->format('H:i:s'),
        ];
    }

    protected function normalizeChartDays($days)
    {
        return max(1, min(365, (int) $days));
    }

    protected function buildAllChartData($days)
    {
        return [
            'revenue' => $this->getRevenueChartData($days),
            'wallets' => $this->getWalletChartData($days),
            'tokens' => $this->getTokenChartData($days),
            'distributions' => $this->getDistributionChartData($days),
        ];
    }

    protected function buildChartDataByType($chartType, $days)
    {
        switch ($chartType) {
            case 'revenue':
                return $this->getRevenueChartData($days);
            case 'wallets':
                return $this->getWalletChartData($days);
            case 'tokens':
                return $this->getTokenChartData($days);
            case 'distributions':
                return $this->getDistributionChartData($days);
            default:
                return [];
        }
    }

    protected function buildTopPerformersData()
    {
        return [
            'top_tokens_by_market_cap' => Cryptocurrency::active()
                ->orderBy('market_cap', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'symbol', 'market_cap', 'logo']),

            'top_users_by_wallet_value' => $this->getTopUsersByWalletValue(),

            'most_distributed_tokens' => Revenue::with('cryptocurrency')
                ->selectRaw('cryptocurrency_id, SUM(distribution_amount) as total_distributed')
                ->distributed()
                ->groupBy('cryptocurrency_id')
                ->orderBy('total_distributed', 'desc')
                ->limit(5)
                ->get(),

            'recent_high_value_distributions' => Revenue::with(['user', 'cryptocurrency'])
                ->distributed()
                ->where('distribution_amount', '>', 1000)
                ->orderBy('distributed_at', 'desc')
                ->limit(5)
                ->get(),
        ];
    }

    protected function buildSystemHealthData()
    {
        return [
            'database_connection' => $this->checkDatabaseConnection(),
            'recent_activity' => $this->checkRecentActivity(),
            'data_integrity' => $this->checkDataIntegrity(),
            'performance_metrics' => $this->getPerformanceMetrics(),
        ];
    }

    protected function getActiveUsersPercentage()
    {
        try {
            $totalUsers = Dashboard::getOverviewStats()['total_users'];
            $activeUsers = Wallet::distinct('user_id')->count('user_id');
            return $totalUsers > 0 ? round(($activeUsers / $totalUsers) * 100, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getDistributionEfficiency()
    {
        try {
            $totalRevenue = Revenue::count();
            $distributedOnTime = Revenue::distributed()
                ->whereRaw('DATEDIFF(distributed_at, created_at) <= 30')
                ->count();
            return $totalRevenue > 0 ? round(($distributedOnTime / $totalRevenue) * 100, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getTotalWalletBalanceUsd()
    {
        try {
            $wallets = Wallet::with('cryptocurrency')->get();
            $total = 0;
            foreach ($wallets as $wallet) {
                if ($wallet->cryptocurrency && $wallet->cryptocurrency->current_price) {
                    $total += $wallet->balance * $wallet->cryptocurrency->current_price;
                }
            }
            return $total;
        } catch (\Exception $e) {
            return 0;
        }
    }

    protected function getRevenueChartData($days)
    {
        return Revenue::selectRaw('DATE(created_at) as date, SUM(revenue_amount) as total')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    protected function getWalletChartData($days)
    {
        return Wallet::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    protected function getTokenChartData($days)
    {
        return Cryptocurrency::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    protected function getDistributionChartData($days)
    {
        return Revenue::selectRaw('DATE(distributed_at) as date, COUNT(*) as count, SUM(distribution_amount) as total_amount')
            ->distributed()
            ->where('distributed_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    protected function getTopUsersByWalletValue()
    {
        try {
            return Wallet::with(['user', 'cryptocurrency'])
                ->get()
                ->groupBy('user_id')
                ->map(function ($wallets, $userId) {
                    $firstWallet = $wallets->first();

                    return [
                        'user_id' => $userId,
                        'user' => $firstWallet ? $firstWallet->user : null,
                        'wallet_count' => $wallets->count(),
                        'total_value_usd' => $wallets->sum(function ($wallet) {
                            return $wallet->balance_usd;
                        }),
                    ];
                })
                ->sortByDesc('total_value_usd')
                ->take(5)
                ->values();
        } catch (\Exception $e) {
            return collect();
        }
    }

    protected function getBasicStats()
    {
        return [
            'total_users' => 0,
            'total_tokens' => 0,
            'total_wallets' => 0,
            'total_revenue_shares' => 0,
            'platform_health_score' => 50
        ];
    }

    protected function checkDatabaseConnection()
    {
        try {
            \DB::connection()->getPdo();
            return ['status' => 'healthy', 'message' => 'Database connection active'];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Database connection failed'];
        }
    }

    protected function checkRecentActivity()
    {
        try {
            $recentActivity = Revenue::where('created_at', '>=', now()->subHour())->count();
            return ['status' => 'healthy', 'recent_count' => $recentActivity];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cannot check recent activity'];
        }
    }

    protected function checkDataIntegrity()
    {
        try {
            $orphanedWallets = Wallet::whereDoesntHave('cryptocurrency')->count();
            $orphanedRevenue = Revenue::whereDoesntHave('cryptocurrency')->count();
            
            return [
                'status' => ($orphanedWallets + $orphanedRevenue) == 0 ? 'healthy' : 'warning',
                'orphaned_wallets' => $orphanedWallets,
                'orphaned_revenue' => $orphanedRevenue
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cannot check data integrity'];
        }
    }

    protected function getPerformanceMetrics()
    {
        try {
            $start = microtime(true);
            Revenue::count();
            $queryTime = microtime(true) - $start;
            
            return [
                'avg_query_time' => round($queryTime * 1000, 2) . 'ms',
                'memory_usage' => round(memory_get_usage(true) / 1024 / 1024, 2) . 'MB'
            ];
        } catch (\Exception $e) {
            return ['status' => 'error', 'message' => 'Cannot get performance metrics'];
        }
    }
}
