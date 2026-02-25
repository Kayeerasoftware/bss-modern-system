<?php

return [

    /*
    |--------------------------------------------------------------------------
    | BSS System Configuration
    |--------------------------------------------------------------------------
    */

    'system_name' => env('BSS_SYSTEM_NAME', 'Business Support System'),
    
    'currency' => env('BSS_CURRENCY', 'UGX'),
    
    'currency_symbol' => env('BSS_CURRENCY_SYMBOL', 'UGX'),
    
    'date_format' => env('BSS_DATE_FORMAT', 'Y-m-d'),
    
    'time_format' => env('BSS_TIME_FORMAT', 'H:i:s'),
    
    'pagination' => [
        'per_page' => env('BSS_PAGINATION_PER_PAGE', 15),
    ],
    
    'billing' => [
        'cycle' => env('BSS_BILLING_CYCLE', 'monthly'),
        'grace_period_days' => env('BSS_GRACE_PERIOD_DAYS', 7),
    ],

    'financial' => [
        'currency' => env('BSS_FINANCIAL_CURRENCY', 'UGX'),
        'interest_rate' => env('BSS_INTEREST_RATE', 5.5),
        'min_savings' => env('BSS_MIN_SAVINGS', 50000),
        'transaction_fee' => env('BSS_TRANSACTION_FEE', 0.01),
        'withdrawal_fee' => env('BSS_WITHDRAWAL_FEE', 0.005),
    ],

    'roles' => [
        'admin',
        'cashier',
        'td',
        'ceo',
        'shareholder',
        'client',
    ],

    'system' => [
        'backup_path' => storage_path('app/backups'),
        'max_file_size' => env('BSS_MAX_FILE_SIZE', 2048),
        'session_timeout' => env('BSS_SESSION_TIMEOUT', 30),
    ],

    'storage' => [
        'profile_pictures' => [
            'members' => 'profile-pictures/members',
            'users' => 'profile-pictures/users',
        ],
        'documents' => [
            'admin' => 'documents/admin',
            'cashier' => 'documents/cashier',
            'td' => 'documents/td',
            'ceo' => 'documents/ceo',
            'shareholder' => 'documents/shareholder',
            'client' => 'documents/client',
        ],
        'reports' => [
            'financial' => 'reports/financial',
            'transactions' => 'reports/transactions',
            'members' => 'reports/members',
            'loans' => 'reports/loans',
            'savings' => 'reports/savings',
            'system' => 'reports/system',
        ],
        'exports' => 'exports',
        'imports' => 'imports',
        'temp' => 'temp',
    ],

];
