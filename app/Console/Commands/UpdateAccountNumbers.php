<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Services\System\AccountNumberService;

class UpdateAccountNumbers extends Command
{
    protected $signature = 'bss:update-account-numbers';
    protected $description = 'Update all existing account numbers to the new BSS-C15-000x format';

    public function handle()
    {
        $this->info('Starting account number update...');
        
        // Update member account numbers
        $this->info('Updating member account numbers...');
        $members = DB::table('members')->whereNull('member_account_number')->orderBy('id')->get();
        
        foreach ($members as $index => $member) {
            $accountNumber = 'BSS-C15-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            DB::table('members')
                ->where('id', $member->id)
                ->update(['member_account_number' => $accountNumber]);
            
            $this->line("Updated member {$member->id}: {$accountNumber}");
        }
        
        // Update savings account numbers
        $this->info('Updating savings account numbers...');
        $savingsAccounts = DB::table('savings_accounts')
            ->where('account_number', 'NOT LIKE', 'SAV-BSS-C15-%')
            ->orderBy('id')
            ->get();
        
        foreach ($savingsAccounts as $index => $account) {
            $accountNumber = 'SAV-BSS-C15-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            DB::table('savings_accounts')
                ->where('id', $account->id)
                ->update(['account_number' => $accountNumber]);
            
            $this->line("Updated savings account {$account->id}: {$accountNumber}");
        }
        
        $this->info('Account number update completed successfully!');
        
        // Show summary
        $memberCount = DB::table('members')->whereNotNull('member_account_number')->count();
        $savingsCount = DB::table('savings_accounts')->where('account_number', 'LIKE', 'SAV-BSS-C15-%')->count();
        
        $this->info("Summary:");
        $this->info("- Members with new account numbers: {$memberCount}");
        $this->info("- Savings accounts with new numbers: {$savingsCount}");
        
        return 0;
    }
}