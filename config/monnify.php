<?php

/*
 * This file is part of the Laravel Monnify package.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Monnify Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your Monnify settings. Monnify is a payment gateway srevice
    | provider.
    |
    |
    */

    /**
     * Api key From Monnify
     */
    'api_key' => env('MONNIFY_API_KEY'),

    /**
     * Secret key From Monnify
     */
    'secret_key' => env('MONNIFY_SECRET_KEY'),

    /**
     * Api contract code From Monnify
     */
    'contract_code' => env('MONNIFY_CONTRACT_CODE'),

    /**
     * Api Wallet number From Monnify
     */
    'wallet_number' => env('MONNIFY_WALLET_ACCOUNT_NUMBER'),

    /**
     * Api Account number From Monnify
     */
    'account_number' => env('MONNIFY_ACCOUNT_NUMBER'),

    /**
     * Monnify environment: SANDBOX or LIVE
     * default: 'SANDBOX'
     */
    'environment' => env('MONNIFY_ENVIRONMENT', 'SANDBOX'),

    /**
     * Base URL for Monnify sandbox API.
     */
    'sandbox_url' => env('MONNIFY_SANDBOX_URL', 'https://sandbox.monnify.com'),

    /**
     * Base URL for Monnify live API.
     */
    'live_url' => env('MONNIFY_LIVE_URL', 'https://api.monnify.com'),
    /**
     * Subaccount code for the hospital (created via Monnify dashboard/API)
     * so appointment fees settle directly to their bank account.
     */
    'hospital_subaccount_code' => env('MONNIFY_HOSPITAL_SUBACCOUNT_CODE'),

    /**
     * Nigerian statutory VAT rate applied on top of Monnify's fee.
     */
    'vat_rate' => (float) env('MONNIFY_VAT_RATE', 0.075),

    /**
     * Monnify's published transfer fee rate and cap, used to ESTIMATE
     * the fee before payment (Monnify doesn't expose a universal
     * pre-payment quote endpoint for every channel). The real fee is
     * confirmed after payment via webhook/verify and overwrites this.
     */
    'transfer_fee_rate' => (float) env('MONNIFY_TRANSFER_FEE_RATE', 0.015),
    'transfer_fee_cap' => (float) env('MONNIFY_TRANSFER_FEE_CAP', 2000),
];
