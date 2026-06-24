<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class KYCVerification
{
    /**
     * KYC Level requirements:
     * Level 0: None - Can browse, limited transactions
     * Level 1: Basic - Email verified, basic info provided - Up to $500/day
     * Level 2: Intermediate - ID uploaded, pending review - Up to $5,000/day
     * Level 3: Full - ID verified, selfie verified - Up to $50,000/day
     */
    
    /**
     * Transaction limits by KYC level (in USD equivalent)
     */
    protected $transactionLimits = [
        0 => ['daily' => 100, 'monthly' => 500, 'per_transaction' => 50],
        1 => ['daily' => 500, 'monthly' => 2000, 'per_transaction' => 250],
        2 => ['daily' => 5000, 'monthly' => 25000, 'per_transaction' => 2500],
        3 => ['daily' => 50000, 'monthly' => 200000, 'per_transaction' => 25000],
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  int  $requiredLevel
     * @return mixed
     */
    public function handle(Request $request, Closure $next, $requiredLevel = 1)
    {
        if (!Auth::check()) {
            return $this->denyAccess($request, 'Authentication required for cryptocurrency transactions.');
        }

        $user = Auth::user();
        $kycLevel = $user->kyc_level ?? 0;
        $kycStatus = $user->kyc_status ?? 'none';

        // Check if KYC is approved
        if ($kycStatus === 'rejected') {
            return $this->denyAccess($request, 'Your KYC verification was rejected. Please contact support or resubmit your documents.');
        }

        if ($kycStatus === 'expired') {
            return $this->denyAccess($request, 'Your KYC verification has expired. Please submit updated documents.');
        }

        // Check if user meets required KYC level
        if ($kycLevel < $requiredLevel) {
            $message = $this->getUpgradeMessage($kycLevel, $requiredLevel);
            return $this->denyAccess($request, $message, $kycLevel);
        }

        // Check if user is flagged for suspicious activity
        if ($user->is_flagged) {
            return $this->denyAccess($request, 'Your account has been flagged for review. Please contact support.');
        }

        // Check if account is locked
        if ($user->locked_until && $user->locked_until > now()) {
            return $this->denyAccess($request, 'Your account is temporarily locked. Please try again later or contact support.');
        }

        // Attach transaction limits to request for downstream use
        $request->merge([
            'kyc_level' => $kycLevel,
            'transaction_limits' => $this->transactionLimits[$kycLevel] ?? $this->transactionLimits[0],
        ]);

        return $next($request);
    }

    /**
     * Get upgrade message based on current and required levels
     */
    protected function getUpgradeMessage($currentLevel, $requiredLevel)
    {
        $messages = [
            0 => 'Please complete basic verification to perform cryptocurrency transactions.',
            1 => 'Please complete intermediate verification (ID upload) to perform this transaction.',
            2 => 'Please complete full verification (ID + selfie) to perform this transaction.',
            3 => 'Full verification required for high-value transactions.',
        ];

        return $messages[$requiredLevel] ?? 'Additional verification required.';
    }

    /**
     * Deny access with appropriate response
     */
    protected function denyAccess(Request $request, $message, $currentLevel = null)
    {
        if ($request->expectsJson() || $request->ajax()) {
            return response()->json([
                'success' => false,
                'error' => 'kyc_required',
                'message' => $message,
                'kyc_level' => $currentLevel,
                'verification_url' => route('my.settings', ['type' => 'verify']),
            ], 403);
        }

        return redirect()
            ->route('my.settings', ['type' => 'verify'])
            ->with('error', $message);
    }
}
