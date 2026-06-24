<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Revenue;
use App\Models\Cryptocurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RevenueController extends Controller
{
    /**
     * Display a listing of revenue shares.
     */
    public function index(Request $request)
    {
        $query = Revenue::query()
            ->with(['user', 'cryptocurrency'])
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  })
                  ->orWhereHas('cryptocurrency', function($cryptoQuery) use ($search) {
                      $cryptoQuery->where('name', 'like', "%{$search}%")
                                 ->orWhere('symbol', 'like', "%{$search}%");
                  });
            });
        }

        // Filter by distribution status
        if ($request->has('status')) {
            switch ($request->status) {
                case 'distributed':
                    $query->distributed();
                    break;
                case 'pending':
                    $query->pending();
                    break;
                case 'overdue':
                    $query->pending()->where('created_at', '<', now()->subDays(30));
                    break;
            }
        }

        // Filter by cryptocurrency
        if ($request->has('cryptocurrency_id') && $request->cryptocurrency_id) {
            $query->byCryptocurrency($request->cryptocurrency_id);
        }

        // Filter by amount range
        if ($request->has('amount_range')) {
            switch ($request->amount_range) {
                case 'small':
                    $query->where('revenue_amount', '<', 100);
                    break;
                case 'medium':
                    $query->whereBetween('revenue_amount', [100, 1000]);
                    break;
                case 'large':
                    $query->where('revenue_amount', '>', 1000);
                    break;
            }
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        
        if (in_array($sortBy, ['created_at', 'revenue_amount', 'distribution_amount', 'percentage', 'distributed_at'])) {
            $query->orderBy($sortBy, $sortDir);
        }

        $revenues = $query->paginate(20);

        // Get statistics
        $stats = [
            'total_revenue_shares' => Revenue::count(),
            'distributed_shares' => Revenue::distributed()->count(),
            'pending_shares' => Revenue::pending()->count(),
            'total_revenue_amount' => Revenue::sum('revenue_amount'),
            'total_distributed_amount' => Revenue::distributed()->sum('distribution_amount'),
            'pending_amount' => Revenue::pending()->sum('distribution_amount'),
            'overdue_shares' => Revenue::pending()->where('created_at', '<', now()->subDays(30))->count(),
        ];

        // Get cryptocurrencies for filter
        $cryptocurrencies = Cryptocurrency::orderBy('name')->get();

        return view('admin.revenue.index', compact('revenues', 'stats', 'cryptocurrencies'));
    }

    /**
     * Display the specified revenue share.
     */
    public function show($id)
    {
        $revenue = Revenue::with(['user', 'cryptocurrency', 'transaction'])->findOrFail($id);
        
        // Get related revenue shares for the same user
        $userRevenues = Revenue::with(['cryptocurrency'])
            ->where('user_id', $revenue->user_id)
            ->where('id', '!=', $revenue->id)
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.revenue.show', compact('revenue', 'userRevenues'));
    }

    /**
     * Mark revenue share as distributed
     */
    public function markDistributed($id)
    {
        $revenue = Revenue::findOrFail($id);
        
        if ($revenue->is_distributed) {
            return redirect()->back()
                ->with('warning', 'Revenue share is already marked as distributed.');
        }

        $revenue->update([
            'is_distributed' => true,
            'distributed_at' => now(),
        ]);
        
        return redirect()->back()
            ->with('success', 'Revenue share marked as distributed successfully.');
    }

    /**
     * Mark revenue share as pending
     */
    public function markPending($id)
    {
        $revenue = Revenue::findOrFail($id);
        
        if (!$revenue->is_distributed) {
            return redirect()->back()
                ->with('warning', 'Revenue share is already pending.');
        }

        $revenue->update([
            'is_distributed' => false,
            'distributed_at' => null,
        ]);
        
        return redirect()->back()
            ->with('success', 'Revenue share marked as pending successfully.');
    }

    /**
     * Get revenue details for modal
     */
    public function getRevenueDetails($id)
    {
        $revenue = Revenue::with(['user', 'cryptocurrency', 'transaction'])->findOrFail($id);
        
        return response()->json([
            'id' => $revenue->id,
            'user_name' => $revenue->user ? $revenue->user->name : 'Unknown User',
            'user_email' => $revenue->user ? $revenue->user->email : 'N/A',
            'cryptocurrency_name' => $revenue->cryptocurrency ? $revenue->cryptocurrency->name : 'Unknown',
            'cryptocurrency_symbol' => $revenue->cryptocurrency ? $revenue->cryptocurrency->symbol : 'N/A',
            'transaction_id' => $revenue->transaction_id ?: 'N/A',
            'percentage' => $revenue->formatted_percentage,
            'revenue_amount' => $revenue->formatted_revenue_amount,
            'revenue_amount_usd' => $revenue->formatted_revenue_amount_usd,
            'distribution_amount' => $revenue->formatted_distribution_amount,
            'distribution_amount_usd' => $revenue->formatted_distribution_amount_usd,
            'is_distributed' => $revenue->is_distributed,
            'status_text' => $revenue->status_text,
            'priority_level' => $revenue->priority_level,
            'priority_color' => $revenue->priority_color,
            'is_overdue' => $revenue->is_overdue,
            'created_at' => $revenue->created_at ? $revenue->created_at->format('M d, Y H:i') : 'N/A',
            'distributed_at' => $revenue->distributed_at ? $revenue->distributed_at->format('M d, Y H:i') : 'Not distributed',
            'time_since_created' => $revenue->time_since_created,
            'time_since_distributed' => $revenue->time_since_distributed,
        ]);
    }

    /**
     * Export revenue data
     */
    public function export(Request $request)
    {
        $query = Revenue::query()->with(['user', 'cryptocurrency']);

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('transaction_id', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status')) {
            switch ($request->status) {
                case 'distributed':
                    $query->distributed();
                    break;
                case 'pending':
                    $query->pending();
                    break;
                case 'overdue':
                    $query->pending()->where('created_at', '<', now()->subDays(30));
                    break;
            }
        }

        $revenues = $query->get();

        $csvData = [];
        $csvData[] = [
            'ID', 'User Name', 'User Email', 'Cryptocurrency', 'Symbol', 
            'Transaction ID', 'Percentage', 'Revenue Amount', 'Revenue USD', 
            'Distribution Amount', 'Distribution USD', 'Status', 'Created At', 'Distributed At'
        ];

        foreach ($revenues as $revenue) {
            $csvData[] = [
                $revenue->id,
                $revenue->user ? $revenue->user->name : 'Unknown',
                $revenue->user ? $revenue->user->email : 'N/A',
                $revenue->cryptocurrency ? $revenue->cryptocurrency->name : 'Unknown',
                $revenue->cryptocurrency ? $revenue->cryptocurrency->symbol : 'N/A',
                $revenue->transaction_id ?: 'N/A',
                $revenue->formatted_percentage,
                $revenue->formatted_revenue_amount,
                $revenue->formatted_revenue_amount_usd,
                $revenue->formatted_distribution_amount,
                $revenue->formatted_distribution_amount_usd,
                $revenue->status_text,
                $revenue->created_at ? $revenue->created_at->format('Y-m-d H:i:s') : 'N/A',
                $revenue->distributed_at ? $revenue->distributed_at->format('Y-m-d H:i:s') : 'Not distributed',
            ];
        }

        $filename = 'revenue_shares_export_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($csvData) {
            $file = fopen('php://output', 'w');
            foreach ($csvData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    /**
     * Get dashboard statistics for revenue
     */
    public function getDashboardStats()
    {
        $stats = [
            'total_revenue' => Revenue::sum('revenue_amount'),
            'distributed_revenue' => Revenue::distributed()->sum('distribution_amount'),
            'pending_revenue' => Revenue::pending()->sum('distribution_amount'),
            'overdue_count' => Revenue::pending()->where('created_at', '<', now()->subDays(30))->count(),
            'recent_distributions' => Revenue::distributed()
                ->where('distributed_at', '>=', now()->subDays(7))
                ->count(),
        ];

        return response()->json($stats);
    }
}