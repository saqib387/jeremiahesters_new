@extends('voyager::master')

@section('page_title', 'Token Management')

@section('page_header')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-8">
                <h1 class="page-title">
                    <i class="voyager-trophy"></i> Token Management
                </h1>
            </div>
            <div class="col-md-4 text-right">
                <a href="{{ route('voyager.tokens.create') }}" class="btn btn-success btn-add-new">
                    <i class="voyager-plus"></i> <span>Add New Token</span>
                </a>
            </div>
        </div>
    </div>
@stop

@section('content')
    <div class="page-content browse container-fluid">
        @include('voyager::alerts')

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-2">
                <div class="panel widget center bgimage" style="background-color:#3498db;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>{{ number_format($stats['total_tokens']) }}</h4>
                        <p>Total Tokens</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel widget center bgimage" style="background-color:#2ecc71;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>{{ number_format($stats['verified_tokens']) }}</h4>
                        <p>Verified</p>
                    </div>
                </div>
            </div>
            <div class="col-md-2">
                <div class="panel widget center bgimage" style="background-color:#f39c12;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>{{ number_format($stats['active_tokens']) }}</h4>
                        <p>Active</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel widget center bgimage" style="background-color:#9b59b6;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>${{ number_format($stats['total_market_cap'], 2) }}</h4>
                        <p>Total Market Cap</p>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="panel widget center bgimage" style="background-color:#e74c3c;">
                    <div class="dimmer"></div>
                    <div class="panel-content">
                        <h4>${{ number_format($stats['total_volume_24h'], 2) }}</h4>
                        <p>24h Volume</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters and Search -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <form method="GET" action="{{ route('voyager.tokens.index') }}" class="form-inline">
                            <div class="form-group">
                                <input type="text" 
                                       name="search" 
                                       class="form-control" 
                                       placeholder="Search tokens..." 
                                       value="{{ request('search') }}">
                            </div>
                            
                            <div class="form-group">
                                <select name="status" class="form-control">
                                    <option value="">All Status</option>
                                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                                    <option value="unverified" {{ request('status') == 'unverified' ? 'selected' : '' }}>Unverified</option>
                                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <select name="token_type" class="form-control">
                                    <option value="">All Types</option>
                                    <option value="utility" {{ request('token_type') == 'utility' ? 'selected' : '' }}>Utility Token</option>
                                    <option value="security" {{ request('token_type') == 'security' ? 'selected' : '' }}>Security Token</option>
                                    <option value="governance" {{ request('token_type') == 'governance' ? 'selected' : '' }}>Governance Token</option>
                                    <option value="nft" {{ request('token_type') == 'nft' ? 'selected' : '' }}>NFT Token</option>
                                </select>
                            </div>
                            
                            <div class="form-group">
                                <select name="network" class="form-control">
                                    <option value="">All Networks</option>
                                    <option value="ETH" {{ request('network') == 'ETH' ? 'selected' : '' }}>Ethereum (ETH)</option>
                                    <option value="BSC" {{ request('network') == 'BSC' ? 'selected' : '' }}>Binance Smart Chain (BSC)</option>
                                    <option value="MATIC" {{ request('network') == 'MATIC' ? 'selected' : '' }}>Polygon (MATIC)</option>
                                    <option value="ARB" {{ request('network') == 'ARB' ? 'selected' : '' }}>Arbitrum (ARB)</option>
                                </select>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="voyager-search"></i> Filter
                            </button>
                            
                            <a href="{{ route('voyager.tokens.index') }}" class="btn btn-default">
                                <i class="voyager-refresh"></i> Clear
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tokens Table -->
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered tokens-table-panel">
                    <div class="panel-body tokens-table-panel-body">
                        <div class="table-responsive tokens-table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Token</th>
                                        <th>Price</th>
                                        <th>24h Change</th>
                                        <th>Market Cap</th>
                                        <th>Volume (24h)</th>
                                        <th>Network</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($tokens as $token)
                                        <tr>
                                            <td>
                                                <div class="media">
                                                    @if($token->logo)
                                                        <div class="media-left">
                                                            <img src="{{ Storage::url($token->logo) }}" 
                                                                 alt="{{ $token->name }}" 
                                                                 class="media-object" 
                                                                 style="width: 40px; height: 40px; border-radius: 50%;">
                                                        </div>
                                                    @endif
                                                    <div class="media-body">
                                                        <strong><a href="{{ route('voyager.tokens.show', $token->id) }}">{{ $token->name }}</a></strong>
                                                        <br>
                                                        <small class="text-muted">{{ $token->symbol }}</small>
                                                        @if($token->is_verified)
                                                            <i class="voyager-check text-success" title="Verified"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <strong>${{ number_format($token->current_price, 8) }}</strong>
                                            </td>
                                            <td>
                                                <span class="{{ $token->price_change_color }}">
                                                    <i class="{{ $token->price_change_icon }}"></i>
                                                    {{ number_format($token->change_24h, 2) }}%
                                                </span>
                                            </td>
                                            <td>${{ number_format((float) $token->market_cap, 2) }}</td>
                                            <td>${{ number_format((float) $token->volume_24h, 2) }}</td>
                                            <td>
                                                <span class="label label-default">{{ $token->display_network_label }}</span>
                                            </td>
                                            <td>
                                                @if($token->is_active)
                                                    <span class="label label-success">Active</span>
                                                @else
                                                    <span class="label label-danger">Inactive</span>
                                                @endif
                                                
                                                @if(!$token->transferable)
                                                    <span class="label label-warning">Frozen</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="btn-group dropdown" role="group">
                                                    <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-toggle="dropdown">
                                                        Actions <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                        <li>
                                                            <a href="{{ route('voyager.tokens.show', $token->id) }}">
                                                                <i class="voyager-eye"></i> View
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('voyager.tokens.edit', $token->id) }}">
                                                                <i class="voyager-edit"></i> Edit
                                                            </a>
                                                        </li>
                                                        <li class="divider"></li>
                                                        <li>
                                                            <a href="{{ route('voyager.tokens.toggle-verification', $token->id) }}" 
                                                               onclick="return confirm('Are you sure?')">
                                                                <i class="voyager-check"></i> 
                                                                {{ $token->is_verified ? 'Unverify' : 'Verify' }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('voyager.tokens.toggle-freeze', $token->id) }}" 
                                                               onclick="return confirm('Are you sure?')">
                                                                <i class="voyager-lock"></i> 
                                                                {{ $token->transferable ? 'Freeze' : 'Unfreeze' }}
                                                            </a>
                                                        </li>
                                                        <li>
                                                            <a href="{{ route('voyager.tokens.toggle-status', $token->id) }}" 
                                                               onclick="return confirm('Are you sure?')">
                                                                <i class="voyager-power"></i> 
                                                                {{ $token->is_active ? 'Deactivate' : 'Activate' }}
                                                            </a>
                                                        </li>
                                                        <li class="divider"></li>
                                                        <li>
                                                            <a href="#" 
                                                               onclick="showSupplyModal({{ $token->id }}, '{{ $token->name }}')"
                                                               data-toggle="modal" 
                                                               data-target="#supplyModal">
                                                                <i class="voyager-dollar"></i> Adjust Supply
                                                            </a>
                                                        </li>
                                                        <li class="divider"></li>
                                                        <li>
                                                            <a href="#" 
                                                               onclick="deleteToken({{ $token->id }})" 
                                                               class="text-danger">
                                                                <i class="voyager-trash"></i> Delete
                                                            </a>
                                                        </li>
                                                    </ul>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="8" class="text-center">
                                                <p>No tokens found.</p>
                                                <a href="{{ route('voyager.tokens.create') }}" class="btn btn-primary">
                                                    <i class="voyager-plus"></i> Create First Token
                                                </a>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Pagination -->
                        @if($tokens->hasPages())
                            <div class="text-center">
                                {{ $tokens->appends(request()->query())->links() }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Supply Adjustment Modal -->
    <div class="modal fade" id="supplyModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form id="supplyForm" method="POST">
                    @csrf
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                        <h4 class="modal-title">Adjust Token Supply</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>Token</label>
                            <input type="text" id="tokenName" class="form-control" disabled>
                        </div>
                        
                        <div class="form-group">
                            <label>Supply Type</label>
                            <select name="supply_type" class="form-control" required>
                                <option value="total_supply">Total Supply</option>
                                <option value="available_supply">Available Supply</option>
                                <option value="circulating_supply">Circulating Supply</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label>New Amount</label>
                            <input type="number" 
                                   name="new_amount" 
                                   class="form-control" 
                                   step="0.01" 
                                   min="0" 
                                   required>
                        </div>
                        
                        <div class="form-group">
                            <label>Reason for Adjustment</label>
                            <textarea name="reason" 
                                      class="form-control" 
                                      rows="3" 
                                      placeholder="Explain why you're adjusting the supply..."
                                      required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Update Supply</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                    <h4 class="modal-title text-danger">Delete Token</h4>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this token? This action cannot be undone.</p>
                    <p><strong>Warning:</strong> This will permanently remove all token data.</p>
                </div>
                <div class="modal-footer">
                    <form id="deleteForm" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Delete Token</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@stop

@section('css')
<style>
@media (min-width: 992px) {
    .tokens-table-panel .tokens-table-responsive {
        overflow: visible;
    }
    .tokens-table-panel .tokens-table-panel-body {
        overflow: visible;
    }
}
.tokens-table-panel .dropdown-menu {
    max-height: none;
    overflow: visible;
}
</style>
@stop

@section('javascript')
<script>
function showSupplyModal(tokenId, tokenName) {
    document.getElementById('tokenName').value = tokenName;
    document.getElementById('supplyForm').action = '{{ route("voyager.tokens.adjust-supply", ":id") }}'.replace(':id', tokenId);
}

function deleteToken(tokenId) {
    document.getElementById('deleteForm').action = '{{ route("voyager.tokens.destroy", ":id") }}'.replace(':id', tokenId);
    $('#deleteModal').modal('show');
}
</script>
@stop