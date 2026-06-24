@extends('voyager::master')

@section('page_title', 'Wallet Details')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-wallet"></i> Wallet Details #{{ $wallet->id }}
        </h1>
        <div class="btn-group pull-right">
            <a href="{{ route('voyager.wallets.index') }}" class="btn btn-default">
                <i class="voyager-list"></i> <span>Back to Wallets</span>
            </a>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content read container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <!-- Left Column - Wallet Info -->
                            <div class="col-md-8">
                                <!-- User Information -->
                                <div class="panel panel-bordered panel-info">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">User Information</h3>
                                    </div>
                                    <div class="panel-body">
                                        @if($wallet->user)
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>User Name:</strong><br>
                                                    {{ $wallet->user->name }}
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Email:</strong><br>
                                                    {{ $wallet->user->email }}
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>User ID:</strong><br>
                                                    #{{ $wallet->user->id }}
                                                </div>
                                                @if(isset($wallet->user->created_at))
                                                    <div class="col-md-6">
                                                        <strong>User Since:</strong><br>
                                                        {{ $wallet->user->created_at->format('M d, Y') }}
                                                    </div>
                                                @endif
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                <strong>Warning:</strong> User information not available.
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Cryptocurrency Information -->
                                <div class="panel panel-bordered panel-success">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Cryptocurrency Information</h3>
                                    </div>
                                    <div class="panel-body">
                                        @if($wallet->cryptocurrency)
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Cryptocurrency:</strong><br>
                                                    <div class="media">
                                                        @if($wallet->cryptocurrency->logo)
                                                            <div class="media-left">
                                                                <img src="{{ Storage::url($wallet->cryptocurrency->logo) }}" 
                                                                     alt="{{ $wallet->cryptocurrency->name }}" 
                                                                     class="media-object" 
                                                                     style="width: 40px; height: 40px; border-radius: 50%;">
                                                            </div>
                                                        @endif
                                                        <div class="media-body">
                                                            <strong>{{ $wallet->cryptocurrency->name }}</strong><br>
                                                            <small class="text-muted">{{ $wallet->cryptocurrency->symbol }}</small>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Current Price:</strong><br>
                                                    ${{ number_format($wallet->cryptocurrency->current_price, 8) }}
                                                </div>
                                            </div>
                                            <br>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <strong>Token Type:</strong><br>
                                                    <span class="label label-info">{{ ucfirst($wallet->cryptocurrency->token_type) }}</span>
                                                </div>
                                                <div class="col-md-6">
                                                    <strong>Network:</strong><br>
                                                    <span class="label label-default">{{ $wallet->cryptocurrency->blockchain_network }}</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="alert alert-warning">
                                                <strong>Warning:</strong> Cryptocurrency information not available.
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Wallet Address -->
                                @if($wallet->wallet_address)
                                    <div class="panel panel-bordered panel-primary">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Wallet Address</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="well">
                                                <strong>Address:</strong><br>
                                                <code style="font-size: 14px; word-break: break-all;">{{ $wallet->wallet_address }}</code>
                                                <button class="btn btn-sm btn-default pull-right" onclick="copyToClipboard('{{ $wallet->wallet_address }}')">
                                                    <i class="voyager-copy"></i> Copy
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <!-- User's Other Wallets -->
                                @if($userWallets->count() > 0)
                                    <div class="panel panel-bordered panel-warning">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">User's Other Wallets</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="table-responsive">
                                                <table class="table table-striped">
                                                    <thead>
                                                        <tr>
                                                            <th>ID</th>
                                                            <th>Cryptocurrency</th>
                                                            <th>Balance</th>
                                                            <th>Status</th>
                                                            <th>Action</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($userWallets as $otherWallet)
                                                            <tr>
                                                                <td>#{{ $otherWallet->id }}</td>
                                                                <td>
                                                                    @if($otherWallet->cryptocurrency)
                                                                        {{ $otherWallet->cryptocurrency->name }} ({{ $otherWallet->cryptocurrency->symbol }})
                                                                    @else
                                                                        Unknown
                                                                    @endif
                                                                </td>
                                                                <td>{{ $otherWallet->formatted_balance }}</td>
                                                                <td>
                                                                    <span class="label {{ $otherWallet->status_badge_class }}">
                                                                        {{ $otherWallet->status_text }}
                                                                    </span>
                                                                </td>
                                                                <td>
                                                                    <a href="{{ route('voyager.wallets.show', $otherWallet->id) }}" class="btn btn-xs btn-primary">
                                                                        <i class="voyager-eye"></i> View
                                                                    </a>
                                                                </td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Right Column - Wallet Details -->
                            <div class="col-md-4">
                                <!-- Balance Information -->
                                <div class="panel panel-bordered">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Balance Information</h3>
                                    </div>
                                    <div class="panel-body text-center">
                                        <h2 class="text-primary">{{ $wallet->formatted_balance }}</h2>
                                        @if($wallet->cryptocurrency)
                                            <p class="text-muted">{{ $wallet->cryptocurrency->symbol }}</p>
                                        @endif
                                        
                                        <hr>
                                        
                                        <h3 class="text-success">{{ $wallet->formatted_balance_usd }}</h3>
                                        <p class="text-muted">USD Value</p>
                                    </div>
                                </div>

                                <!-- Wallet Status -->
                                <div class="panel panel-bordered">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Wallet Status</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group text-center">
                                            <span class="label {{ $wallet->status_badge_class }}" style="font-size: 16px; padding: 8px 12px;">
                                                {{ $wallet->status_text }}
                                            </span>
                                        </div>
                                        
                                        <div class="form-group">
                                            <strong>Has Private Key:</strong><br>
                                            @if($wallet->has_private_key)
                                                <i class="voyager-check text-success"></i> Yes
                                            @else
                                                <i class="voyager-x text-danger"></i> No
                                            @endif
                                        </div>
                                        
                                        <div class="form-group">
                                            <strong>Has Address:</strong><br>
                                            @if($wallet->has_address)
                                                <i class="voyager-check text-success"></i> Yes
                                            @else
                                                <i class="voyager-x text-danger"></i> No
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                <!-- Actions -->
                                <div class="panel panel-bordered">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Actions</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <a href="{{ route('voyager.wallets.toggle-status', $wallet->id) }}" 
                                               class="btn btn-warning btn-block"
                                               onclick="return confirm('Are you sure you want to {{ $wallet->is_active ? 'deactivate' : 'activate' }} this wallet?')">
                                                <i class="voyager-power"></i> 
                                                {{ $wallet->is_active ? 'Deactivate' : 'Activate' }} Wallet
                                            </a>
                                        </div>
                                        
                                        <div class="form-group">
                                            <a href="{{ route('voyager.wallets.index') }}" class="btn btn-default btn-block">
                                                <i class="voyager-list"></i> Back to Wallets
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <!-- Timestamps -->
                                <div class="panel panel-bordered">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">Timestamps</h3>
                                    </div>
                                    <div class="panel-body">
                                        <div class="form-group">
                                            <strong>Created:</strong><br>
                                            {{ $wallet->created_at ? $wallet->created_at->format('M d, Y H:i') : 'N/A' }}
                                        </div>
                                        <div class="form-group">
                                            <strong>Last Updated:</strong><br>
                                            {{ $wallet->updated_at ? $wallet->updated_at->format('M d, Y H:i') : 'N/A' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(function() {
        // Show success message
        toastr.success('Address copied to clipboard!');
    }, function(err) {
        // Fallback for older browsers
        var textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            toastr.success('Address copied to clipboard!');
        } catch (err) {
            toastr.error('Failed to copy address');
        }
        document.body.removeChild(textArea);
    });
}
</script>
@stop