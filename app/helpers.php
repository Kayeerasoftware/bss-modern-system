<?php

if (!function_exists('setting')) {
    function setting($key, $default = null)
    {
        return \App\Models\Setting::get($key, $default);
    }
}

if (!function_exists('generate_member_account_number')) {
    function generate_member_account_number()
    {
        return \App\Services\System\AccountNumberService::generateMemberAccountNumber();
    }
}

if (!function_exists('generate_savings_account_number')) {
    function generate_savings_account_number()
    {
        return \App\Services\System\AccountNumberService::generateSavingsAccountNumber();
    }
}

if (!function_exists('format_account_number')) {
    function format_account_number($type, $number = null)
    {
        if ($type === 'member') {
            return $number ? $number : generate_member_account_number();
        } elseif ($type === 'savings') {
            return $number ? $number : generate_savings_account_number();
        }
        return $number;
    }
}
