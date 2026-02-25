<?php

if (!function_exists('format_currency')) {
    function format_currency($amount, $currency = null)
    {
        $currency = $currency ?? config('bss.currency_symbol', 'UGX');
        return $currency . ' ' . number_format($amount, 2);
    }
}

if (!function_exists('format_date')) {
    function format_date($date, $format = null)
    {
        $format = $format ?? config('bss.date_format', 'Y-m-d');
        return $date ? date($format, strtotime($date)) : '';
    }
}

if (!function_exists('calculate_interest')) {
    function calculate_interest($principal, $rate, $months)
    {
        return ($principal * $rate * $months) / (100 * 12);
    }
}

if (!function_exists('generate_member_id')) {
    function generate_member_id()
    {
        $lastMember = \App\Models\Member::latest('id')->first();
        $nextId = $lastMember ? $lastMember->id + 1 : 1;
        return 'MEM' . str_pad($nextId, 6, '0', STR_PAD_LEFT);
    }
}

if (!function_exists('generate_transaction_ref')) {
    function generate_transaction_ref($type = 'TXN')
    {
        return $type . '-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -6));
    }
}

if (!function_exists('user_has_role')) {
    function user_has_role($role)
    {
        return auth()->check() && auth()->user()->role === $role;
    }
}

if (!function_exists('user_has_permission')) {
    function user_has_permission($permission)
    {
        if (!auth()->check()) {
            return false;
        }
        
        $user = auth()->user();
        if ($user->role === 'admin') {
            return true;
        }
        
        $rolePermissions = config('permissions.' . $user->role, []);
        return in_array($permission, $rolePermissions);
    }
}
