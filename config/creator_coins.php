<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Creator Coins (non-cashable loyalty points)
    |--------------------------------------------------------------------------
    */

    // Platform's cut of each points purchase, in percent. The creator receives the rest as
    // withdrawable platform credits.
    'platform_fee_percentage' => env('CREATOR_COINS_PLATFORM_FEE', 10),

    // Bounds on the price a creator may set for one point (in platform credits).
    'min_price_per_point' => env('CREATOR_COINS_MIN_PRICE', 0.01),
    'max_price_per_point' => env('CREATOR_COINS_MAX_PRICE', 10000),

    // Max points a fan can buy in a single transaction (sanity bound).
    'max_purchase_points' => env('CREATOR_COINS_MAX_PURCHASE', 1000000),
];
