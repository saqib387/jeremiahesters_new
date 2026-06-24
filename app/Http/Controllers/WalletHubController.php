<?php

namespace App\Http\Controllers;

use App\Models\CreatorCoinBalance;
use App\Models\CreatorCoinTransaction;
use App\Models\NFT;
use App\Models\NFTTransaction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

/**
 * The integrated, non-custodial crypto wallet hub: one place where a user sees their on-chain
 * wallet (address, native balance), their NFTs, their creator points, their platform credits,
 * and a unified activity feed. It only AGGREGATES data the app already owns + an optional live
 * RPC balance read — the app never holds the user's keys.
 */
class WalletHubController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $address = $user->wallet_address;

        // Holdings ------------------------------------------------------------------------
        $credits = (float) (optional($user->wallet)->total ?? 0);

        $nftQuery = NFT::query()->where('status', '!=', NFT::STATUS_MINT_FAILED);
        if ($address) {
            $nftQuery->where(fn ($q) => $q->ownedByAddress($address)->orWhere('user_id', $user->id));
        } else {
            $nftQuery->where('user_id', $user->id);
        }
        $nftCount = (clone $nftQuery)->count();
        $recentNfts = $nftQuery->latest()->limit(6)->get();

        $coinBalances = CreatorCoinBalance::with('coin')
            ->where('user_id', $user->id)
            ->where('balance', '>', 0)
            ->get();

        $nativeBalance = $address ? $this->readNativeBalance($address) : null;

        // Unified activity feed -----------------------------------------------------------
        $activity = $this->activityFeed($user->id, $address);

        return view('wallet.index', compact(
            'user', 'address', 'credits', 'nftCount', 'recentNfts', 'coinBalances', 'nativeBalance', 'activity'
        ));
    }

    /**
     * Best-effort on-chain native balance via JSON-RPC. Returns a float (token units) or null.
     * Wrapped so a dev environment / unreachable RPC never breaks the page.
     */
    private function readNativeBalance(string $address): ?float
    {
        $rpc = config('web3.rpc_url');
        if (!$rpc) {
            return null;
        }

        try {
            $resp = Http::timeout(5)->post($rpc, [
                'jsonrpc' => '2.0',
                'method' => 'eth_getBalance',
                'params' => [$address, 'latest'],
                'id' => 1,
            ]);
            $hex = data_get($resp->json(), 'result');
            if (!is_string($hex) || !str_starts_with($hex, '0x')) {
                return null;
            }

            return (float) (hexdec(substr($hex, 2)) / 1e18);
        } catch (\Throwable $e) {
            return null;
        }
    }

    /**
     * Merge creator-coin and NFT activity into one normalized, time-sorted timeline.
     *
     * @return \Illuminate\Support\Collection<int,array<string,mixed>>
     */
    private function activityFeed(int $userId, ?string $address)
    {
        $coinRows = CreatorCoinTransaction::with('coin')
            ->where('user_id', $userId)
            ->latest()
            ->limit(25)
            ->get()
            ->map(fn ($t) => [
                'kind' => 'coin',
                'label' => ucfirst($t->type) . ' ' . optional($t->coin)->symbol,
                'amount' => rtrim(rtrim(number_format((float) $t->points, 4, '.', ''), '0'), '.'),
                'in' => $t->type !== CreatorCoinTransaction::TYPE_SPEND,
                'at' => $t->created_at,
            ]);

        $nftRows = NFTTransaction::with('nft')
            ->whereHas('nft', fn ($q) => $q->where('user_id', $userId))
            ->latest()
            ->limit(25)
            ->get()
            ->map(fn ($t) => [
                'kind' => 'nft',
                'label' => ucfirst($t->type) . ' · ' . (optional($t->nft)->name ?? 'NFT'),
                'amount' => null,
                'in' => $address ? (strtolower((string) $t->to_address) === strtolower($address)) : true,
                'at' => $t->created_at,
            ]);

        return $coinRows->concat($nftRows)
            ->sortByDesc(fn ($r) => optional($r['at'])->timestamp ?? 0)
            ->take(25)
            ->values();
    }
}
