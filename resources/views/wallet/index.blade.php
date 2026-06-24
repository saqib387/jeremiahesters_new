@extends('layouts.generic')

@section('content')
<div class="container py-4">
    <h1 class="h3 mb-4"><i class="fas fa-wallet"></i> My Wallet</h1>

    @include('nft.partials.wallet-connect')

    @if($address)
        {{-- Holdings overview ------------------------------------------------------------ --}}
        <div class="row">
            <div class="col-6 col-md-3 mb-3">
                <div class="card shadow-sm h-100"><div class="card-body text-center">
                    <div class="text-muted small">Native balance</div>
                    <div class="h5 mb-0">{{ $nativeBalance !== null ? number_format($nativeBalance, 4) : '—' }}</div>
                    <div class="small text-muted">{{ strtoupper(config('web3.network')) }}</div>
                </div></div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card shadow-sm h-100"><div class="card-body text-center">
                    <div class="text-muted small">Platform credits</div>
                    <div class="h5 mb-0">{{ number_format($credits, 2) }}</div>
                    <a href="{{ route('cryptocurrency.deposit') }}" class="small">Add funds</a>
                </div></div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card shadow-sm h-100"><div class="card-body text-center">
                    <div class="text-muted small">NFTs owned</div>
                    <div class="h5 mb-0">{{ $nftCount }}</div>
                    <a href="{{ route('nft.my-nfts') }}" class="small">View</a>
                </div></div>
            </div>
            <div class="col-6 col-md-3 mb-3">
                <div class="card shadow-sm h-100"><div class="card-body text-center">
                    <div class="text-muted small">Creator coins held</div>
                    <div class="h5 mb-0">{{ $coinBalances->count() }}</div>
                    <a href="{{ route('creator-coins.holdings') }}" class="small">View</a>
                </div></div>
            </div>
        </div>

        <div class="row">
            {{-- Receive ------------------------------------------------------------------- --}}
            <div class="col-md-5 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header">Receive</div>
                    <div class="card-body text-center">
                        <div id="wallet-qr" class="d-flex justify-content-center mb-3"></div>
                        <div class="input-group">
                            <input type="text" class="form-control form-control-sm" id="wallet-address-field"
                                   value="{{ $address }}" readonly>
                            <div class="input-group-append">
                                <button class="btn btn-outline-secondary btn-sm" type="button" id="copy-address-btn">
                                    <i class="fas fa-copy"></i>
                                </button>
                            </div>
                        </div>
                        <small class="text-muted">Share this address to receive tokens/NFTs.</small>
                    </div>
                </div>
            </div>

            {{-- Send ---------------------------------------------------------------------- --}}
            <div class="col-md-7 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header">Send {{ strtoupper(config('web3.network')) }}</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="small">Recipient address</label>
                            <input type="text" id="send-to" class="form-control" placeholder="0x...">
                        </div>
                        <div class="form-group">
                            <label class="small">Amount</label>
                            <input type="number" id="send-amount" class="form-control" step="0.0001" min="0" placeholder="0.0">
                        </div>
                        <button class="btn btn-primary" id="send-btn"><i class="fas fa-paper-plane"></i> Send</button>
                        <div id="send-status" class="small mt-2 text-muted"></div>
                        <p class="small text-muted mt-2 mb-0">
                            Sends are signed by your own wallet — the platform never holds your keys.
                            Requires a connected browser wallet.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Activity feed ----------------------------------------------------------------- --}}
        <div class="card shadow-sm">
            <div class="card-header">Recent activity</div>
            <ul class="list-group list-group-flush">
                @forelse($activity as $item)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>
                            <i class="fas {{ $item['kind'] === 'nft' ? 'fa-image' : 'fa-coins' }} text-muted mr-2"></i>
                            {{ $item['label'] }}
                            @if($item['at'])<span class="text-muted small ml-2">{{ $item['at']->diffForHumans() }}</span>@endif
                        </span>
                        @if($item['amount'] !== null)
                            <span class="{{ $item['in'] ? 'text-success' : 'text-danger' }}">
                                {{ $item['in'] ? '+' : '-' }}{{ $item['amount'] }}
                            </span>
                        @else
                            <span class="badge badge-light">{{ $item['in'] ? 'in' : 'out' }}</span>
                        @endif
                    </li>
                @empty
                    <li class="list-group-item text-muted">No activity yet.</li>
                @endforelse
            </ul>
        </div>
    @else
        <div class="alert alert-warning">
            <i class="fas fa-wallet"></i> Connect a wallet above to see your balances, NFTs, creator coins and activity.
        </div>
    @endif
</div>

@if($address)
<script src="{{ asset('libs/easyqrcodejs/dist/easy.qrcode.min.js') }}"></script>
<script>
(function () {
    var address = @json($address);

    // QR
    try {
        if (window.QRCode) {
            new QRCode(document.getElementById('wallet-qr'), { text: address, width: 160, height: 160, correctLevel: QRCode.CorrectLevel.M });
        }
    } catch (e) { /* QR optional */ }

    // Copy
    var copyBtn = document.getElementById('copy-address-btn');
    if (copyBtn) copyBtn.addEventListener('click', function () {
        var field = document.getElementById('wallet-address-field');
        field.select();
        if (navigator.clipboard) navigator.clipboard.writeText(address);
        else document.execCommand('copy');
        copyBtn.innerHTML = '<i class="fas fa-check"></i>';
        setTimeout(function () { copyBtn.innerHTML = '<i class="fas fa-copy"></i>'; }, 1500);
    });

    // Send (non-custodial: signed by the user's own injected wallet)
    var sendBtn = document.getElementById('send-btn');
    var status = document.getElementById('send-status');
    if (sendBtn) sendBtn.addEventListener('click', async function () {
        var to = document.getElementById('send-to').value.trim();
        var amount = parseFloat(document.getElementById('send-amount').value);
        if (!/^0x[a-fA-F0-9]{40}$/.test(to)) { status.className = 'small mt-2 text-danger'; status.textContent = 'Enter a valid 0x recipient address.'; return; }
        if (!(amount > 0)) { status.className = 'small mt-2 text-danger'; status.textContent = 'Enter an amount greater than zero.'; return; }
        if (typeof window.ethereum === 'undefined') {
            status.className = 'small mt-2 text-danger';
            status.textContent = 'No browser wallet detected. Connect a wallet (e.g. MetaMask) to send.';
            return;
        }
        try {
            status.className = 'small mt-2 text-muted'; status.textContent = 'Confirm the transaction in your wallet…';
            var accounts = await window.ethereum.request({ method: 'eth_requestAccounts' });
            var valueWei = '0x' + Math.floor(amount * 1e18).toString(16);
            var hash = await window.ethereum.request({
                method: 'eth_sendTransaction',
                params: [{ from: accounts[0], to: to, value: valueWei }]
            });
            status.className = 'small mt-2 text-success'; status.textContent = 'Sent. Tx: ' + hash;
        } catch (e) {
            status.className = 'small mt-2 text-danger'; status.textContent = 'Send failed: ' + (e.message || e);
        }
    });
})();
</script>
@endif
@endsection
