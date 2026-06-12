<?php

return [
    'admin' => [
        'view_dashboard',
        'manage_users',
        'manage_members',
        'manage_loans',
        'manage_transactions',
        'manage_projects',
        'manage_settings',
        'view_reports',
        'manage_backups',
        'view_audit_logs',
    ],

    'ceo' => [
        'view_dashboard',
        'view_reports',
        'view_financial_summary',
        'view_members',
        'view_loans',
        'view_projects',
        'approve_loans',
    ],

    'td' => [
        'view_dashboard',
        'manage_projects',
        'view_reports',
        'view_members',
    ],

    'cashier' => [
        'view_dashboard',
        'process_transactions',
        'view_members',
        'register_members',
        'process_deposits',
        'process_withdrawals',
    ],

    'shareholder' => [
        'view_dashboard',
        'view_portfolio',
        'view_investments',
        'make_investments',
        'view_dividends',
    ],

    'client' => [
        'view_dashboard',
        'view_balance',
        'view_transactions',
        'make_deposits',
        'request_withdrawals',
        'view_loans',
        'apply_for_loan',
    ],
];
