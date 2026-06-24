<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Wallet;
use App\Models\Cryptocurrency;
use Illuminate\Http\Request;

class WalletController extends Controller
{
    /**
     * Display a listing of wallets.
     */
    public function index(Request $request)
    {
        $query = Wallet::query()
            ->with(['user', 'cryptocurrency'])
            ->orderBy('created_at', 'desc');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('wallet_address', 'like', "%{$search}%")
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

        // Filter by status
        if ($request->has('status')) {
            switch ($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'inactive':
                    $query->inactive();
                    break;
                case 'with_balance':
                    $query->withBalance();
                    break;
                case 'empty':
                    $query->empty();
                    break;
            }
        }

        // Filter by cryptocurrency
        if ($request->has('cryptocurrency_id') && $request->cryptocurrency_id) {
            $query->where('cryptocurrency_id', $request->cryptocurrency_id);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        
        // Allow sorting by balance, created_at, updated_at
        if (in_array($sortBy, ['balance', 'created_at', 'updated_at'])) {
            $query->orderBy($sortBy, $sortDir);
        }

        $wallets = $query->paginate(20);

        // Get statistics
        $stats = [
            'total_wallets' => Wallet::count(),
            'active_wallets' => Wallet::active()->count(),
            'wallets_with_balance' => Wallet::withBalance()->count(),
            'total_balance_usd' => $this->getTotalBalanceUsd(),
            'unique_users' => Wallet::distinct('user_id')->count('user_id'),
        ];

        // Get cryptocurrencies for filter
        $cryptocurrencies = Cryptocurrency::orderBy('name')->get();

        return view('admin.wallets.index', compact('wallets', 'stats', 'cryptocurrencies'));
    }

    /**
     * Display the specified wallet.
     */
    public function show($id)
    {
        $wallet = Wallet::with(['user', 'cryptocurrency'])->findOrFail($id);
        
        // Get related wallets for the same user
        $userWallets = Wallet::with(['cryptocurrency'])
            ->where('user_id', $wallet->user_id)
            ->where('id', '!=', $wallet->id)
            ->get();

        return view('admin.wallets.show', compact('wallet', 'userWallets'));
    }

    /**
     * Toggle wallet status (activate/deactivate)
     */
    public function toggleStatus($id)
    {
        $wallet = Wallet::findOrFail($id);
        $wallet->update(['is_active' => !$wallet->is_active]);
        
        $status = $wallet->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Wallet has been {$status} successfully.");
    }

    /**
     * Get wallet details for modal
     */
    public function getWalletDetails($id)
    {
        $wallet = Wallet::with(['user', 'cryptocurrency'])->findOrFail($id);
        
        return response()->json([
            'id' => $wallet->id,
            'user_name' => $wallet->user ? $wallet->user->name : 'Unknown User',
            'user_email' => $wallet->user ? $wallet->user->email : 'N/A',
            'cryptocurrency_name' => $wallet->cryptocurrency ? $wallet->cryptocurrency->name : 'Unknown',
            'cryptocurrency_symbol' => $wallet->cryptocurrency ? $wallet->cryptocurrency->symbol : 'N/A',
            'balance' => $wallet->formatted_balance,
            'balance_usd' => $wallet->formatted_balance_usd,
            'wallet_address' => $wallet->wallet_address ?: 'No Address',
            'masked_address' => $wallet->masked_address,
            'has_private_key' => $wallet->has_private_key,
            'is_active' => $wallet->is_active,
            'status_text' => $wallet->status_text,
            'created_at' => $wallet->created_at ? $wallet->created_at->format('M d, Y H:i') : 'N/A',
            'updated_at' => $wallet->updated_at ? $wallet->updated_at->format('M d, Y H:i') : 'N/A',
        ]);
    }

    /**
     * Calculate total balance in USD
     */
    protected function getTotalBalanceUsd()
    {
        $wallets = Wallet::with('cryptocurrency')->get();
        $totalUsd = 0;

        foreach ($wallets as $wallet) {
            if ($wallet->cryptocurrency && $wallet->cryptocurrency->current_price) {
                $totalUsd += $wallet->balance * $wallet->cryptocurrency->current_price;
            }
        }

        return $totalUsd;
    }

    /**
     * Export wallet data
     */
    public function export(Request $request)
    {
        $query = Wallet::query()->with(['user', 'cryptocurrency']);

        // Apply same filters as index
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('wallet_address', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                               ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->has('status')) {
            switch ($request->status) {
                case 'active':
                    $query->active();
                    break;
                case 'inactive':
                    $query->inactive();
                    break;
                case 'with_balance':
                    $query->withBalance();
                    break;
                case 'empty':
                    $query->empty();
                    break;
            }
        }

        $wallets = $query->get();

        $csvData = [];
        $csvData[] = ['ID', 'User Name', 'User Email', 'Cryptocurrency', 'Symbol', 'Balance', 'Balance USD', 'Wallet Address', 'Status', 'Created At'];

        foreach ($wallets as $wallet) {
            $csvData[] = [
                $wallet->id,
                $wallet->user ? $wallet->user->name : 'Unknown',
                $wallet->user ? $wallet->user->email : 'N/A',
                $wallet->cryptocurrency ? $wallet->cryptocurrency->name : 'Unknown',
                $wallet->cryptocurrency ? $wallet->cryptocurrency->symbol : 'N/A',
                $wallet->formatted_balance,
                $wallet->formatted_balance_usd,
                $wallet->wallet_address ?: 'No Address',
                $wallet->status_text,
                $wallet->created_at ? $wallet->created_at->format('Y-m-d H:i:s') : 'N/A',
            ];
        }

        $filename = 'wallets_export_' . date('Y-m-d_H-i-s') . '.csv';
        
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
}