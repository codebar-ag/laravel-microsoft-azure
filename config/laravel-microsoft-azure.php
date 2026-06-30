<?php

return [

    'default' => env('MICROSOFT_AZURE_CONNECTION', 'default'),

    'timeout' => (int) env('MICROSOFT_AZURE_TIMEOUT', 60),

    /*
    |--------------------------------------------------------------------------
    | Named connections (multi-tenant via config)
    |--------------------------------------------------------------------------
    */
    'connections' => [
        'default' => [
            'tenant_id' => env('MICROSOFT_AZURE_TENANT_ID'),
            'client_id' => env('MICROSOFT_AZURE_CLIENT_ID'),
            'client_secret' => env('MICROSOFT_AZURE_CLIENT_SECRET'),
            'subscription_id' => env('MICROSOFT_AZURE_SUBSCRIPTION_ID'),
            'cache_driver' => env('MICROSOFT_AZURE_CACHE_DRIVER', 'file'),
            'cache_lifetime_in_seconds' => (int) env('MICROSOFT_AZURE_CACHE_LIFETIME_IN_SECONDS', 3300),
            'request_timeout_in_seconds' => (int) env('MICROSOFT_AZURE_REQUEST_TIMEOUT_IN_SECONDS', 60),
        ],
    ],

    'retry' => [
        'enabled' => (bool) env('MICROSOFT_AZURE_RETRY_ENABLED', true),
        'times' => (int) env('MICROSOFT_AZURE_RETRY_TIMES', 3),
        'base_interval_ms' => (int) env('MICROSOFT_AZURE_RETRY_BASE_INTERVAL_MS', 250),
        'max_interval_ms' => (int) env('MICROSOFT_AZURE_RETRY_MAX_INTERVAL_MS', 10000),
    ],

    'rate_limit' => [
        'enabled' => (bool) env('MICROSOFT_AZURE_RATE_LIMIT_ENABLED', false),
        'allow' => (int) env('MICROSOFT_AZURE_RATE_LIMIT_ALLOW', 60),
        'per_seconds' => (int) env('MICROSOFT_AZURE_RATE_LIMIT_PER_SECONDS', 60),
    ],

    'debug' => [
        'capture_bodies' => (bool) env('MICROSOFT_AZURE_DEBUG_CAPTURE_BODIES', false),
    ],

];
