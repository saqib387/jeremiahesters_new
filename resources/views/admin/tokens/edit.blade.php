@extends('voyager::master')

@section('page_title', 'Edit Token')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-edit"></i> Edit Token: {{ $cryptocurrency->name }}
        </h1>
        <a href="{{ route('voyager.tokens.index') }}" class="btn btn-warning btn-add-new">
            <i class="voyager-list"></i> <span>Back to Tokens</span>
        </a>
    </div>
@stop

@section('content')
    <div class="page-content edit-add container-fluid">
        @include('voyager::alerts')
        
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <form role="form" 
                              action="{{ route('voyager.tokens.update', $cryptocurrency->id) }}" 
                              method="POST" 
                              enctype="multipart/form-data">
                            @csrf
                            @method('PUT')
                            
                            <div class="row">
                                <!-- Left Column -->
                                <div class="col-md-8">
                                    <!-- Basic Information -->
                                    <div class="panel panel-bordered panel-info">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Basic Information</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Token Name <span class="text-danger">*</span></label>
                                                        <input type="text" 
                                                               name="name" 
                                                               class="form-control" 
                                                               value="{{ old('name', $cryptocurrency->name) }}" 
                                                               required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Symbol <span class="text-danger">*</span></label>
                                                        <input type="text" 
                                                               name="symbol" 
                                                               class="form-control" 
                                                               value="{{ old('symbol', $cryptocurrency->symbol) }}" 
                                                               maxlength="10" 
                                                               required>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>Description</label>
                                                <textarea name="description" 
                                                          class="form-control" 
                                                          rows="4">{{ old('description', $cryptocurrency->description) }}</textarea>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Website</label>
                                                        <input type="url" 
                                                               name="website" 
                                                               class="form-control" 
                                                               value="{{ old('website', $cryptocurrency->website) }}">
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Whitepaper URL</label>
                                                        <input type="url" 
                                                               name="whitepaper" 
                                                               class="form-control" 
                                                               value="{{ old('whitepaper', $cryptocurrency->whitepaper) }}">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Price & Market Data -->
                                    <div class="panel panel-bordered panel-success">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Price & Market Data</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Initial Price <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">$</span>
                                                            <input type="number" 
                                                                   name="initial_price" 
                                                                   class="form-control" 
                                                                   value="{{ old('initial_price', $cryptocurrency->initial_price) }}" 
                                                                   step="0.00000001" 
                                                                   min="0" 
                                                                   required>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Current Price <span class="text-danger">*</span></label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">$</span>
                                                            <input type="number" 
                                                                   name="current_price" 
                                                                   class="form-control" 
                                                                   value="{{ old('current_price', $cryptocurrency->current_price) }}" 
                                                                   step="0.00000001" 
                                                                   min="0" 
                                                                   required>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Market Cap</label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">$</span>
                                                            <input type="number" 
                                                                   name="market_cap" 
                                                                   class="form-control" 
                                                                   value="{{ old('market_cap', $cryptocurrency->market_cap) }}" 
                                                                   step="0.01" 
                                                                   min="0">
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>24h Volume</label>
                                                        <div class="input-group">
                                                            <span class="input-group-addon">$</span>
                                                            <input type="number" 
                                                                   name="volume_24h" 
                                                                   class="form-control" 
                                                                   value="{{ old('volume_24h', $cryptocurrency->volume_24h) }}" 
                                                                   step="0.01" 
                                                                   min="0">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Supply Information -->
                                    <div class="panel panel-bordered panel-warning">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Supply Information</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Total Supply <span class="text-danger">*</span></label>
                                                        <input type="number" 
                                                               name="total_supply" 
                                                               class="form-control" 
                                                               value="{{ old('total_supply', $cryptocurrency->total_supply) }}" 
                                                               step="0.01" 
                                                               min="0" 
                                                               required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Available Supply <span class="text-danger">*</span></label>
                                                        <input type="number" 
                                                               name="available_supply" 
                                                               class="form-control" 
                                                               value="{{ old('available_supply', $cryptocurrency->available_supply) }}" 
                                                               step="0.01" 
                                                               min="0" 
                                                               required>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Circulating Supply <span class="text-danger">*</span></label>
                                                        <input type="number" 
                                                               name="circulating_supply" 
                                                               class="form-control" 
                                                               value="{{ old('circulating_supply', $cryptocurrency->circulating_supply) }}" 
                                                               step="0.01" 
                                                               min="0" 
                                                               required>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Max Supply</label>
                                                        <input type="number" 
                                                               name="max_supply" 
                                                               class="form-control" 
                                                               value="{{ old('max_supply', $cryptocurrency->max_supply) }}" 
                                                               step="0.01" 
                                                               min="0">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Blockchain & Contract -->
                                    <div class="panel panel-bordered panel-primary">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Blockchain & Contract</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Blockchain Network <span class="text-danger">*</span></label>
                                                        <select name="blockchain_network" class="form-control" required>
                                                            @foreach($networks as $key => $value)
                                                                <option value="{{ $key }}" 
                                                                        {{ old('blockchain_network', $cryptocurrency->blockchain_network) == $key ? 'selected' : '' }}>
                                                                    {{ $value }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group">
                                                        <label>Token Type <span class="text-danger">*</span></label>
                                                        <select name="token_type" class="form-control" required>
                                                            @foreach($tokenTypes as $key => $value)
                                                                <option value="{{ $key }}" 
                                                                        {{ old('token_type', $cryptocurrency->token_type) == $key ? 'selected' : '' }}>
                                                                    {{ $value }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>Contract Address</label>
                                                <input type="text" 
                                                       name="contract_address" 
                                                       class="form-control" 
                                                       value="{{ old('contract_address', $cryptocurrency->contract_address) }}">
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Right Column -->
                                <div class="col-md-4">
                                    <!-- Logo & Settings -->
                                    <div class="panel panel-bordered">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Logo & Settings</h3>
                                        </div>
                                        <div class="panel-body">
                                            <!-- Current Logo -->
                                            @if($cryptocurrency->logo)
                                                <div class="form-group text-center">
                                                    <label>Current Logo</label>
                                                    <br>
                                                    <img src="{{ Storage::url($cryptocurrency->logo) }}" 
                                                         alt="{{ $cryptocurrency->name }}" 
                                                         style="width: 100px; height: 100px; border-radius: 50%;">
                                                </div>
                                            @endif
                                            
                                            <div class="form-group">
                                                <label>Upload New Logo</label>
                                                <input type="file" 
                                                       name="logo" 
                                                       class="form-control" 
                                                       accept="image/*">
                                                <small class="help-block">JPG, PNG, GIF, SVG. Max 2MB.</small>
                                            </div>
                                            
                                            <!-- Creator -->
                                            <div class="form-group">
                                                <label>Creator</label>
                                                <select name="creator_user_id" class="form-control">
                                                    <option value="">Select Creator</option>
                                                    @foreach($users as $user)
                                                        <option value="{{ $user->id }}" 
                                                                {{ old('creator_user_id', $cryptocurrency->creator_user_id) == $user->id ? 'selected' : '' }}>
                                                            {{ $user->name }} ({{ $user->email }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Fee Structure -->
                                    <div class="panel panel-bordered panel-info">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Fee Structure</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label>Creator Fee %</label>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           name="creator_fee_percentage" 
                                                           class="form-control" 
                                                           value="{{ old('creator_fee_percentage', $cryptocurrency->creator_fee_percentage) }}" 
                                                           step="0.01" 
                                                           min="0" 
                                                           max="100">
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>Platform Fee %</label>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           name="platform_fee_percentage" 
                                                           class="form-control" 
                                                           value="{{ old('platform_fee_percentage', $cryptocurrency->platform_fee_percentage) }}" 
                                                           step="0.01" 
                                                           min="0" 
                                                           max="100">
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>Liquidity Pool %</label>
                                                <div class="input-group">
                                                    <input type="number" 
                                                           name="liquidity_pool_percentage" 
                                                           class="form-control" 
                                                           value="{{ old('liquidity_pool_percentage', $cryptocurrency->liquidity_pool_percentage) }}" 
                                                           step="0.01" 
                                                           min="0" 
                                                           max="100">
                                                    <span class="input-group-addon">%</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Token Features -->
                                    <div class="panel panel-bordered panel-success">
                                        <div class="panel-heading">
                                            <h3 class="panel-title">Token Features</h3>
                                        </div>
                                        <div class="panel-body">
                                            <div class="form-group">
                                                <label>
                                                    <input type="checkbox" 
                                                           name="enable_burning" 
                                                           value="1" 
                                                           {{ old('enable_burning', $cryptocurrency->enable_burning) ? 'checked' : '' }}>
                                                    Enable Token Burning
                                                </label>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>
                                                    <input type="checkbox" 
                                                           name="enable_minting" 
                                                           value="1" 
                                                           {{ old('enable_minting', $cryptocurrency->enable_minting) ? 'checked' : '' }}>
                                                    Enable Token Minting
                                                </label>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>
                                                    <input type="checkbox" 
                                                           name="transferable" 
                                                           value="1" 
                                                           {{ old('transferable', $cryptocurrency->transferable) ? 'checked' : '' }}>
                                                    Transferable
                                                </label>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>
                                                    <input type="checkbox" 
                                                           name="is_verified" 
                                                           value="1" 
                                                           {{ old('is_verified', $cryptocurrency->is_verified) ? 'checked' : '' }}>
                                                    Verified Token
                                                </label>
                                            </div>
                                            
                                            <div class="form-group">
                                                <label>
                                                    <input type="checkbox" 
                                                           name="is_active" 
                                                           value="1" 
                                                           {{ old('is_active', $cryptocurrency->is_active) ? 'checked' : '' }}>
                                                    Active Token
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Submit Buttons -->
                            <div class="panel-footer">
                                <button type="submit" class="btn btn-primary">
                                    <i class="voyager-check"></i> Update Token
                                </button>
                                <a href="{{ route('voyager.tokens.index') }}" class="btn btn-default">
                                    <i class="voyager-x"></i> Cancel
                                </a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
<script>
$(document).ready(function() {
    // Calculate market cap automatically
    $('input[name="current_price"], input[name="circulating_supply"]').on('input', function() {
        var price = parseFloat($('input[name="current_price"]').val()) || 0;
        var supply = parseFloat($('input[name="circulating_supply"]').val()) || 0;
        var marketCap = price * supply;
        $('input[name="market_cap"]').val(marketCap.toFixed(2));
    });
});
</script>
@stop