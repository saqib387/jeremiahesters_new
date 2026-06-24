<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Cryptocurrency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;

class CryptocurrencyController extends Controller
{
    /**
     * Display a listing of the tokens.
     */
    public function index(Request $request)
    {
        $query = Cryptocurrency::query()->with('creator');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('symbol', 'like', "%{$search}%")
                  ->orWhere('contract_address', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->has('status')) {
            switch ($request->status) {
                case 'verified':
                    $query->verified();
                    break;
                case 'unverified':
                    $query->where('is_verified', false);
                    break;
                case 'active':
                    $query->active();
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
            }
        }

        // Filter by token type
        if ($request->has('token_type') && $request->token_type) {
            $query->byType($request->token_type);
        }

        // Filter by blockchain network
        if ($request->has('network') && $request->network) {
            $query->byNetwork($request->network);
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDir = $request->get('sort_dir', 'desc');
        $query->orderBy($sortBy, $sortDir);

        $tokens = $query->paginate(20);

        // Get statistics
        $stats = [
            'total_tokens' => Cryptocurrency::count(),
            'verified_tokens' => Cryptocurrency::verified()->count(),
            'active_tokens' => Cryptocurrency::active()->count(),
            'total_market_cap' => Cryptocurrency::sum('market_cap'),
            'total_volume_24h' => Cryptocurrency::sum('volume_24h'),
        ];

        return view('admin.tokens.index', compact('tokens', 'stats'));
    }

    /**
     * Show the form for creating a new token.
     */
    public function create()
    {
        $users = $this->getUsers();
        $tokenTypes = [
            'utility' => 'Utility Token',
            'security' => 'Security Token',
            'governance' => 'Governance Token',
            'nft' => 'NFT Token',
        ];
        $networks = [
            'ETH' => 'Ethereum (ETH)',
            'BSC' => 'Binance Smart Chain (BSC)',
            'MATIC' => 'Polygon (MATIC)',
            'ARB' => 'Arbitrum (ARB)',
        ];
        
        return view('admin.tokens.create', compact('users', 'tokenTypes', 'networks'));
    }

    /**
     * Store a newly created token.
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'symbol' => 'required|string|max:10|unique:cryptocurrencies',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:191',
            'whitepaper' => 'nullable|url|max:191',
            'initial_price' => 'required|numeric|min:0',
            'current_price' => 'required|numeric|min:0',
            'total_supply' => 'required|numeric|min:0',
            'available_supply' => 'required|numeric|min:0',
            'circulating_supply' => 'required|numeric|min:0',
            'max_supply' => 'nullable|numeric|min:0',
            'blockchain_network' => 'required|string|max:191',
            'token_type' => 'required|in:utility,security,governance,nft',
            'contract_address' => 'nullable|string|max:191',
            'creator_fee_percentage' => 'required|numeric|min:0|max:100',
            'platform_fee_percentage' => 'required|numeric|min:0|max:100',
            'liquidity_pool_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $validator->after(function ($validator) use ($request) {
            $max = $request->input('max_supply');
            $circ = $request->input('circulating_supply');
            if ($max !== null && $max !== '' && $circ !== null && (float) $circ > (float) $max) {
                $validator->errors()->add('circulating_supply', __('Circulating supply cannot exceed max supply.'));
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $logoPath = $request->file('logo')->store('token-logos', 'public');
            $data['logo'] = $logoPath;
        }

        // Set boolean values
        $data['enable_burning'] = $request->has('enable_burning');
        $data['enable_minting'] = $request->has('enable_minting');
        $data['transferable'] = $request->has('transferable');
        $data['is_verified'] = $request->has('is_verified');
        $data['is_active'] = $request->has('is_active');

        Cryptocurrency::create($data);

        return redirect()->route('voyager.tokens.index')
            ->with('success', 'Token created successfully.');
    }

    /**
     * Display the specified token.
     */
    public function show($id)
    {
        $cryptocurrency = Cryptocurrency::findOrFail($id);
        $cryptocurrency->load('creator');
        return view('admin.tokens.show', compact('cryptocurrency'));
    }

    /**
     * Show the form for editing the specified token.
     */
    public function edit($id)
    {
        $cryptocurrency = Cryptocurrency::findOrFail($id);
        $users = $this->getUsers();
        $tokenTypes = [
            'utility' => 'Utility Token',
            'security' => 'Security Token',
            'governance' => 'Governance Token',
            'nft' => 'NFT Token',
        ];
        $networks = [
            'ETH' => 'Ethereum (ETH)',
            'BSC' => 'Binance Smart Chain (BSC)',
            'MATIC' => 'Polygon (MATIC)',
            'ARB' => 'Arbitrum (ARB)',
        ];
        
        return view('admin.tokens.edit', compact('cryptocurrency', 'users', 'tokenTypes', 'networks'));
    }

    /**
     * Update the specified token.
     */
    public function update(Request $request, $id)
    {
        $cryptocurrency = Cryptocurrency::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:191',
            'symbol' => 'required|string|max:10|unique:cryptocurrencies,symbol,' . $cryptocurrency->id,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'description' => 'nullable|string',
            'website' => 'nullable|url|max:191',
            'whitepaper' => 'nullable|url|max:191',
            'initial_price' => 'required|numeric|min:0',
            'current_price' => 'required|numeric|min:0',
            'total_supply' => 'required|numeric|min:0',
            'available_supply' => 'required|numeric|min:0',
            'circulating_supply' => 'required|numeric|min:0',
            'max_supply' => 'nullable|numeric|min:0',
            'blockchain_network' => 'required|string|max:191',
            'token_type' => 'required|in:utility,security,governance,nft',
            'contract_address' => 'nullable|string|max:191',
            'creator_fee_percentage' => 'required|numeric|min:0|max:100',
            'platform_fee_percentage' => 'required|numeric|min:0|max:100',
            'liquidity_pool_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $validator->after(function ($validator) use ($request) {
            $max = $request->input('max_supply');
            $circ = $request->input('circulating_supply');
            if ($max !== null && $max !== '' && $circ !== null && (float) $circ > (float) $max) {
                $validator->errors()->add('circulating_supply', __('Circulating supply cannot exceed max supply.'));
            }
        });

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->all();

        // Handle logo upload
        if ($request->hasFile('logo')) {
            // Delete old logo if exists
            if ($cryptocurrency->logo && Storage::disk('public')->exists($cryptocurrency->logo)) {
                Storage::disk('public')->delete($cryptocurrency->logo);
            }
            $logoPath = $request->file('logo')->store('token-logos', 'public');
            $data['logo'] = $logoPath;
        }

        // Set boolean values
        $data['enable_burning'] = $request->has('enable_burning');
        $data['enable_minting'] = $request->has('enable_minting');
        $data['transferable'] = $request->has('transferable');
        $data['is_verified'] = $request->has('is_verified');
        $data['is_active'] = $request->has('is_active');

        $cryptocurrency->update($data);

        return redirect()->route('voyager.tokens.index')
            ->with('success', 'Token updated successfully.');
    }

    /**
     * Remove the specified token.
     */
    public function destroy($id)
    {
        $cryptocurrency = Cryptocurrency::findOrFail($id);
        
        // Delete logo if exists
        if ($cryptocurrency->logo && Storage::disk('public')->exists($cryptocurrency->logo)) {
            Storage::disk('public')->delete($cryptocurrency->logo);
        }

        $cryptocurrency->delete();

        return redirect()->route('voyager.tokens.index')
            ->with('success', 'Token deleted successfully.');
    }

    /**
     * Freeze/Unfreeze token (toggle transferable)
     */
    public function toggleFreeze($id)
    {
        $cryptocurrency = Cryptocurrency::findOrFail($id);
        $cryptocurrency->update(['transferable' => !$cryptocurrency->transferable]);
        
        $status = $cryptocurrency->transferable ? 'unfrozen' : 'frozen';
        return redirect()->back()
            ->with('success', "Token has been {$status} successfully.");
    }

    /**
     * Verify/Unverify token
     */
    public function toggleVerification($id)
    {
        $cryptocurrency = Cryptocurrency::findOrFail($id);
        $cryptocurrency->update(['is_verified' => !$cryptocurrency->is_verified]);
        
        $status = $cryptocurrency->is_verified ? 'verified' : 'unverified';
        return redirect()->back()
            ->with('success', "Token has been {$status} successfully.");
    }

    /**
     * Activate/Deactivate token
     */
    public function toggleStatus($id)
    {
        $cryptocurrency = Cryptocurrency::findOrFail($id);
        $cryptocurrency->update(['is_active' => !$cryptocurrency->is_active]);
        
        $status = $cryptocurrency->is_active ? 'activated' : 'deactivated';
        return redirect()->back()
            ->with('success', "Token has been {$status} successfully.");
    }

    /**
     * Adjust token supply
     */
    public function adjustSupply(Request $request, $id)
    {
        $cryptocurrency = Cryptocurrency::findOrFail($id);
        
        $validator = Validator::make($request->all(), [
            'supply_type' => 'required|in:total_supply,available_supply,circulating_supply',
            'new_amount' => 'required|numeric|min:0',
            'reason' => 'required|string|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $supplyType = $request->supply_type;
        $oldAmount = $cryptocurrency->{$supplyType};
        $newAmount = $request->new_amount;

        $maxSupply = $cryptocurrency->max_supply;
        if ($supplyType === 'circulating_supply' && $maxSupply !== null && (float) $newAmount > (float) $maxSupply) {
            return redirect()->back()
                ->withErrors(['new_amount' => __('Circulating supply cannot exceed max supply.')])
                ->withInput();
        }

        $cryptocurrency->update([$supplyType => $newAmount]);

        return redirect()->back()
            ->with('success', 'Token supply adjusted successfully.');
    }

    /**
     * Get users from the correct User model
     */
    protected function getUsers()
    {
        if (class_exists('\App\Models\User')) {
            return \App\Models\User::all();
        } elseif (class_exists('\TCG\Voyager\Models\User')) {
            return \TCG\Voyager\Models\User::all();
        } elseif (class_exists('\App\User')) {
            return \App\User::all();
        }
        
        return collect(); // Return empty collection if no user model found
    }
}