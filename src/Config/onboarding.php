<?php

return [
    'api_prefix' => env('ONBOARDING_API_PREFIX', 'api/v1'),
    'enable_web_interface' => env('ONBOARDING_ENABLE_WEB_INTERFACE', true),
    'web_prefix' => env('ONBOARDING_WEB_PREFIX', 'applications'),
    'subdomain_base_domain' => env('ONBOARDING_SUBDOMAIN_BASE_DOMAIN', 'akasigroup.local'),
    'brand_domain' => env('ONBOARDING_BRAND_DOMAIN', 'akasigroup.local'),
    'brand_name' => env('ONBOARDING_BRAND_NAME', 'Akasi Group'),
    'rate_limits' => [
        'start' => [
            'max_attempts' => env('ONBOARDING_RATE_LIMIT_START', 10),
            'decay_minutes' => env('ONBOARDING_RATE_LIMIT_START_DECAY', 60),
        ],
        'provision' => [
            'max_attempts' => env('ONBOARDING_RATE_LIMIT_PROVISION', 5),
            'decay_minutes' => env('ONBOARDING_RATE_LIMIT_PROVISION_DECAY', 60),
        ],
        'status' => [
            'max_attempts' => env('ONBOARDING_RATE_LIMIT_STATUS', 60),
            'decay_minutes' => env('ONBOARDING_RATE_LIMIT_STATUS_DECAY', 1),
        ],
    ],
    'subdomain' => [
        'reserved_names' => [
            'admin', 'api', 'www', 'mail', 'ftp', 'localhost', 
            'test', 'dev', 'staging', 'prod', 'app'
        ],
        'max_length' => env('ONBOARDING_SUBDOMAIN_MAX_LENGTH', 63),
        'min_length' => env('ONBOARDING_SUBDOMAIN_MIN_LENGTH', 3),
    ],
    'database_prefix' => env('ONBOARDING_DATABASE_PREFIX', 'onb_'),
    'database' => [
        'create_for_applications' => env('ONBOARDING_CREATE_DB_FOR_APPS', false),
        'prefix' => env('ONBOARDING_DB_PREFIX', 'app_'),
    ],
    'webhooks' => [
        'enabled' => env('ONBOARDING_WEBHOOKS_ENABLED', true),
        'timeout' => env('ONBOARDING_WEBHOOK_TIMEOUT', 30),
        'retry_attempts' => env('ONBOARDING_WEBHOOK_RETRY_ATTEMPTS', 3),
    ],
    'dns' => [
        'provider' => env('ONBOARDING_DNS_PROVIDER', 'stub'),
        'auto_configure' => env('ONBOARDING_DNS_AUTO_CONFIGURE', false),
    ],
    'ssl' => [
        'provider' => env('ONBOARDING_SSL_PROVIDER', 'stub'),
        'auto_configure' => env('ONBOARDING_SSL_AUTO_CONFIGURE', false),
    ],
];
