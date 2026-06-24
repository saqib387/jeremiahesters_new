<?php

namespace App\Http\Controllers;

use App\Model\Cryptocurrency;
use App\Model\CryptoTransaction;
use App\Model\CryptoWallet;
use App\Providers\CryptocurrencyServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CryptoWalletController extends Controller
{
    protected $cryptoProvider;

    public function __construct(CryptocurrencyServiceProvider $cryptoProvider)
    {
        $this->cryptoProvider = $cryptoProvider;
    }

    /**
     * Display a listing of the user's wallets.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $wallets = Auth::user()->cryptoWallets()
            ->with('cryptocurrency')
            ->get();
            
        $totalValue = Auth::user()->total_crypto_value;
        
        return view('cryptocurrency.wallet.index', [
            'wallets' => $wallets,
            'totalValue' => $totalValue
        ]);
    }

    /**
     * Display the specified wallet.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $wallet = CryptoWallet::where('id', $id)
            ->where('user_id', Auth::id())
            ->with('cryptocurrency')
            ->firstOrFail();
            
        $transactions = CryptoTransaction::where(function($query) {
                $query->where('buyer_user_id', Auth::id())
                    ->orWhere('seller_user_id', Auth::id());
            })
            ->where('cryptocurrency_id', $wallet->cryptocurrency_id)
            ->orderBy('created_at', 'desc')
            ->paginate(10);
            
        return view('cryptocurrency.wallet.show', [
            'wallet' => $wallet,
            'transactions' => $transactions
        ]);
    }

    /**
     * Transfer tokens to another user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function transfer(Request $request)
    {
        $request->validate([
            'cryptocurrency_id' => 'required|exists:cryptocurrencies,id',
            'recipient_username' => 'required|exists:users,username',
            'amount' => 'required|numeric|min:1'
        ]);
        
        $cryptocurrencyId = $request->input('cryptocurrency_id');
        $recipientUsername = $request->input('recipient_username');
        $amount = $request->input('amount');
        
        // Get sender's wallet
        $senderWallet = Auth::user()->getCryptoWallet($cryptocurrencyId);
        
        if (!$senderWallet || !$senderWallet->hasSufficientBalance($amount)) {
            return back()->with('error', __('Insufficient balance for transfer.'));
        }
        
        // Get recipient
        $recipient = \App\User::where('username', $recipientUsername)->first();
        
        if (!$recipient) {
            return back()->with('error', __('Recipient not found.'));
        }
        
        // Get or create recipient's wallet
        $recipientWallet = $this->cryptoProvider->createWallet($recipient->id, $cryptocurrencyId);
        
        // Create transfer transaction
        $transaction = new CryptoTransaction();
        $transaction->cryptocurrency_id = $cryptocurrencyId;
        $transaction->buyer_user_id = $recipient->id; // Recipient is buyer in this case
        $transaction->seller_user_id = Auth::id(); // Sender is seller
        $transaction->type = CryptoTransaction::TRANSFER_TYPE;
        $transaction->amount = $amount;
        $transaction->price_per_token = $senderWallet->cryptocurrency->current_price;
        $transaction->total_price = $amount * $senderWallet->cryptocurrency->current_price;
        $transaction->fee_amount = 0; // No fee for transfers
        $transaction->status = CryptoTransaction::COMPLETED_STATUS;
        $transaction->save();
        
        // Update balances
        $senderWallet->balance -= $amount;
        $senderWallet->save();
        
        $recipientWallet->balance += $amount;
        $recipientWallet->save();
        
        return redirect()->route('cryptocurrency.wallet.index')
            ->with('success', __('Transfer completed successfully.'));
    }
} 