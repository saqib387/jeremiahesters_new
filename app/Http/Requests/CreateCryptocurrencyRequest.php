<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateCryptocurrencyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'symbol' => 'required|string|max:10|unique:cryptocurrencies',
            'description' => 'nullable|string',
            'logo' => 'nullable|image|max:2048',
            'initial_price' => 'required|numeric|min:0.00000001',
            'total_supply' => 'required|integer|min:1',
            'blockchain_network' => 'required|string|in:ethereum,binance_smart_chain,polygon',
            'creator_fee_percentage' => 'nullable|numeric|min:0|max:15',
            'platform_fee_percentage' => 'nullable|numeric|min:0|max:5',
            'creator_allocation' => 'nullable|integer|min:0'
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'name.required' => __('The cryptocurrency name is required.'),
            'symbol.required' => __('The cryptocurrency symbol is required.'),
            'symbol.unique' => __('This symbol is already in use.'),
            'initial_price.required' => __('The initial price is required.'),
            'initial_price.min' => __('The initial price must be at least 0.00000001.'),
            'total_supply.required' => __('The total supply is required.'),
            'total_supply.min' => __('The total supply must be at least 1.'),
            'blockchain_network.required' => __('The blockchain network is required.'),
            'blockchain_network.in' => __('The selected blockchain network is invalid.'),
            'creator_fee_percentage.max' => __('The creator fee cannot exceed 15%.'),
            'platform_fee_percentage.max' => __('The platform fee cannot exceed 5%.')
        ];
    }
} 