<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Dashboard extends Model
{
    // This is a utility model for dashboard statistics
    // No database table needed - just for organizing dashboard logic

    /**
     * Get overview statistics for the dashboard
     */
    public static function getOverviewStats()
    {
        return [
            // Users stats
            'total_users' => self::getUserCount(),
            'new_users_today' => self::getNewUsersToday(),
            'new_users_this_week' => self::getNewUsersThisWeek(),
            
            // Cryptocurrency/Token stats  
            'total_tokens' => Cryptocurrency::count(),
            'active_tokens' => Cryptocurrency::active()->count(),
            'verified_tokens' => Cryptocurrency::verified()->count(),
            'total_market_cap' => Cryptocurrency::sum('market_cap'),
            
            // Wallet stats
            'total_wallets' => Wallet::count(),
            'active_wallets' => Wallet::active()->count(),
            'wallets_with_balance' => Wallet::withBalance()->count(),
            'total_wallet_balance_usd' => self::getTotalWalletBalanceUsd(),
            
            // Revenue stats
            'total_revenue_shares' => Revenue::count(),
            'pending_distributions' => Revenue::pending()->count(),
            'overdue_distributions' => Revenue::pending()->where('created_at', '<', now()->subDays(30))->count(),
            'total_distributed_amount' => Revenue::distributed()->sum('distribution_amount'),
            'pending_distribution_amount' => Revenue::pending()->sum('distribution_amount'),
            
            // Platform health indicators
            'platform_health_score' => self::calculatePlatformHealthScore(),
        ];
    }

    /**
     * Get recent activity data
     */
    public static function getRecentActivity()
    {
        return [
            'recent_tokens' => Cryptocurrency::orderBy('created_at', 'desc')->limit(5)->get(),
            'recent_wallets' => Wallet::with(['user', 'cryptocurrency'])
                ->orderBy('created_at', 'desc')->limit(5)->get(),
            'recent_distributions' => Revenue::with(['user', 'cryptocurrency'])
                ->distributed()
                ->orderBy('distributed_at', 'desc')->limit(5)->get(),
            'pending_high_priority' => Revenue::pending()
                ->where(function($query) {
                    $query->where('created_at', '<', now()->subDays(30))
                          ->orWhere('distribution_amount', '>', 1000);
                })
                ->orderBy('created_at', 'asc')->limit(5)->get(),
        ];
    }

    /**
     * Get chart data for trends
     */
    public static function getChartData()
    {
        return [
            'revenue_trend' => self::getRevenueTrend(),
            'wallet_growth' => self::getWalletGrowth(),
            'token_creation_trend' => self::getTokenCreationTrend(),
            'distribution_performance' => self::getDistributionPerformance(),
        ];
    }

    /**
     * Get platform alerts and warnings
     */
    public static function getPlatformAlerts()
    {
        $alerts = [];

        // Check for overdue distributions
        $overdueCount = Revenue::pending()->where('created_at', '<', now()->subDays(30))->count();
        if ($overdueCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Overdue Distributions',
                'message' => "{$overdueCount} revenue distributions are overdue (>30 days)",
                'action_url' => route('voyager.revenue.index', ['status' => 'overdue']),
                'action_text' => 'View Overdue'
            ];
        }

        // Check for inactive tokens with high market cap
        $inactiveHighValueTokens = Cryptocurrency::where('is_active', false)
            ->where('market_cap', '>', 100000)->count();
        if ($inactiveHighValueTokens > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Inactive High-Value Tokens',
                'message' => "{$inactiveHighValueTokens} high-value tokens are currently inactive",
                'action_url' => route('voyager.tokens.index', ['status' => 'inactive']),
                'action_text' => 'Review Tokens'
            ];
        }

        // Check for wallets with large balances
        $highBalanceWallets = Wallet::where('balance', '>', 10000)->count();
        if ($highBalanceWallets > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'High Balance Wallets',
                'message' => "{$highBalanceWallets} wallets have balances exceeding 10,000 tokens",
                'action_url' => route('voyager.wallets.index', ['sort_by' => 'balance', 'sort_dir' => 'desc']),
                'action_text' => 'View Wallets'
            ];
        }

        // Check for unverified tokens
        $unverifiedTokens = Cryptocurrency::where('is_verified', false)->count();
        if ($unverifiedTokens > 5) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Unverified Tokens',
                'message' => "{$unverifiedTokens} tokens are awaiting verification",
                'action_url' => route('voyager.tokens.index', ['status' => 'unverified']),
                'action_text' => 'Verify Tokens'
            ];
        }

        return $alerts;
    }

    /**
     * Get user count (handles different User model locations)
     */
    protected static function getUserCount()
    {
        try {
            if (class_exists('\App\Models\User')) {
                return \App\Models\User::count();
            } elseif (class_exists('\TCG\Voyager\Models\User')) {
                return \TCG\Voyager\Models\User::count();
            } elseif (class_exists('\App\User')) {
                return \App\User::count();
            }
        } catch (\Exception $e) {
            return 0;
        }
        return 0;
    }

    /**
     * Get new users today
     */
    protected static function getNewUsersToday()
    {
        try {
            if (class_exists('\App\Models\User')) {
                return \App\Models\User::whereDate('created_at', today())->count();
            } elseif (class_exists('\TCG\Voyager\Models\User')) {
                return \TCG\Voyager\Models\User::whereDate('created_at', today())->count();
            } elseif (class_exists('\App\User')) {
                return \App\User::whereDate('created_at', today())->count();
            }
        } catch (\Exception $e) {
            return 0;
        }
        return 0;
    }

    /**
     * Get new users this week
     */
    protected static function getNewUsersThisWeek()
    {
        try {
            if (class_exists('\App\Models\User')) {
                return \App\Models\User::where('created_at', '>=', now()->startOfWeek())->count();
            } elseif (class_exists('\TCG\Voyager\Models\User')) {
                return \TCG\Voyager\Models\User::where('created_at', '>=', now()->startOfWeek())->count();
            } elseif (class_exists('\App\User')) {
                return \App\User::where('created_at', '>=', now()->startOfWeek())->count();
            }
        } catch (\Exception $e) {
            return 0;
        }
        return 0;
    }

    /**
     * Calculate total wallet balance in USD
     */
    protected static function getTotalWalletBalanceUsd()
    {
        try {
            $wallets = Wallet::with('cryptocurrency')->get();
            $totalUsd = 0;

            foreach ($wallets as $wallet) {
                if ($wallet->cryptocurrency && $wallet->cryptocurrency->current_price) {
                    $totalUsd += $wallet->balance * $wallet->cryptocurrency->current_price;
                }
            }

            return $totalUsd;
        } catch (\Exception $e) {
            return 0;
        }
    }

    /**
     * Calculate platform health score (0-100)
     */
    protected static function calculatePlatformHealthScore()
    {
        $score = 100;

        try {
            // Deduct points for overdue distributions
            $overdueCount = Revenue::pending()->where('created_at', '<', now()->subDays(30))->count();
            $score -= min($overdueCount * 5, 30); // Max 30 points deduction

            // Deduct points for inactive high-value tokens
            $inactiveHighValue = Cryptocurrency::where('is_active', false)
                ->where('market_cap', '>', 100000)->count();
            $score -= min($inactiveHighValue * 10, 20); // Max 20 points deduction

            // Add points for recent activity
            $recentActivity = Revenue::where('distributed_at', '>=', now()->subDays(7))->count();
            $score += min($recentActivity * 2, 10); // Max 10 points addition

        } catch (\Exception $e) {
            $score = 85; // Default score if calculation fails
        }

        return max(0, min(100, $score)); // Ensure score is between 0-100
    }

    /**
     * Get revenue trend for the last 30 days
     */
    protected static function getRevenueTrend()
    {
        try {
            return Revenue::selectRaw('DATE(created_at) as date, SUM(revenue_amount) as total')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get wallet growth trend
     */
    protected static function getWalletGrowth()
    {
        try {
            return Wallet::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get token creation trend
     */
    protected static function getTokenCreationTrend()
    {
        try {
            return Cryptocurrency::selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get();
        } catch (\Exception $e) {
            return collect();
        }
    }

    /**
     * Get distribution performance (percentage of on-time distributions)
     */
    protected static function getDistributionPerformance()
    {
        try {
            $total = Revenue::count();
            $onTime = Revenue::distributed()
                ->whereRaw('DATEDIFF(distributed_at, created_at) <= 30')
                ->count();
            
            return $total > 0 ? round(($onTime / $total) * 100, 2) : 0;
        } catch (\Exception $e) {
            return 0;
        }
    }
}
