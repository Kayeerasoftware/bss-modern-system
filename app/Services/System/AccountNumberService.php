<?php

namespace App\Services\System;

use Illuminate\Support\Facades\DB;

class AccountNumberService
{
    /**
     * Generate member account number in format: BSS-C15-000x
     */
    public static function generateMemberAccountNumber(): string
    {
        $lastNumber = DB::table('members')
            ->whereNotNull('member_account_number')
            ->orderByRaw('CAST(SUBSTRING(member_account_number, -4) AS UNSIGNED) DESC')
            ->value('member_account_number');
        
        if ($lastNumber) {
            $lastSequence = (int) substr($lastNumber, -4);
            $nextSequence = $lastSequence + 1;
        } else {
            $nextSequence = 1;
        }
        
        return 'BSS-C15-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Generate savings account number in format: SAV-BSS-C15-000x
     */
    public static function generateSavingsAccountNumber(): string
    {
        $lastNumber = DB::table('savings_accounts')
            ->where('account_number', 'LIKE', 'SAV-BSS-C15-%')
            ->orderByRaw('CAST(SUBSTRING(account_number, -4) AS UNSIGNED) DESC')
            ->value('account_number');
        
        if ($lastNumber) {
            $lastSequence = (int) substr($lastNumber, -4);
            $nextSequence = $lastSequence + 1;
        } else {
            $nextSequence = 1;
        }
        
        return 'SAV-BSS-C15-' . str_pad($nextSequence, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Check if member account number is valid
     */
    public static function isValidMemberAccountNumber(string $accountNumber): bool
    {
        return preg_match('/^BSS-C15-\d{4}$/', $accountNumber) === 1;
    }
    
    /**
     * Check if savings account number is valid
     */
    public static function isValidSavingsAccountNumber(string $accountNumber): bool
    {
        return preg_match('/^SAV-BSS-C15-\d{4}$/', $accountNumber) === 1;
    }
}