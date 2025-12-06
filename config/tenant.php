<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Tenant Identification
    |--------------------------------------------------------------------------
    |
    | Configure how tenants are identified in the application.
    | Options: 'domain', 'subdomain', 'path', 'header'
    |
    */
    'identification' => env('TENANT_IDENTIFICATION', 'subdomain'),

    /*
    |--------------------------------------------------------------------------
    | Central Domains
    |--------------------------------------------------------------------------
    |
    | Domains that should not be treated as tenant subdomains.
    | These are used for super admin access and landing pages.
    |
    */
    'central_domains' => [
        'localhost',
        '127.0.0.1',
        env('APP_DOMAIN', 'hrm-pro.test'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Model
    |--------------------------------------------------------------------------
    |
    | The model that represents a tenant in your application.
    |
    */
    'tenant_model' => \App\Models\Tenant::class,

    /*
    |--------------------------------------------------------------------------
    | Tenant Column
    |--------------------------------------------------------------------------
    |
    | The column name used to store the tenant identifier in tenant-scoped tables.
    |
    */
    'tenant_column' => 'tenant_id',

    /*
    |--------------------------------------------------------------------------
    | Exempt Models
    |--------------------------------------------------------------------------
    |
    | Models that should not have tenant scoping applied automatically.
    | These are typically global models like User, Tenant, etc.
    |
    */
    'exempt_models' => [
        \App\Models\User::class,
        \App\Models\Tenant::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Single database multi-tenancy configuration.
    |
    */
    'database' => [
        'strategy' => 'single', // single or multiple
        'prefix' => env('TENANT_DB_PREFIX', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Tenant Features
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific tenant features.
    |
    */
    'features' => [
        'custom_domains' => env('TENANT_CUSTOM_DOMAINS', false),
        'subdomain_routing' => env('TENANT_SUBDOMAIN_ROUTING', true),
        'tenant_branding' => true,
        'isolated_cache' => true,
        'isolated_filesystem' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    |
    | Available subscription plans for tenants.
    |
    */
    'subscription_plans' => [
        'trial' => [
            'name' => 'Trial',
            'max_employees' => 10,
            'duration_days' => 14,
            'features' => ['basic_attendance', 'basic_leave', 'basic_payroll'],
        ],
        'basic' => [
            'name' => 'Basic',
            'max_employees' => 50,
            'features' => ['attendance', 'leave', 'payroll', 'reports'],
        ],
        'premium' => [
            'name' => 'Premium',
            'max_employees' => 200,
            'features' => ['attendance', 'leave', 'payroll', 'reports', 'recruitment', 'performance', 'assets'],
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'max_employees' => null, // unlimited
            'features' => ['all'],
        ],
    ],
];
