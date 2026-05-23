<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        
        // Add member_account_number to members table
        DB::statement('ALTER TABLE members ADD COLUMN member_account_number VARCHAR(20) UNIQUE NULL AFTER member_number');
        
        // Update existing members with new account numbers in BSS-C15-000x format
        $members = DB::table('members')->orderBy('id')->get();
        foreach ($members as $index => $member) {
            $accountNumber = 'BSS-C15-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            DB::table('members')
                ->where('id', $member->id)
                ->update(['member_account_number' => $accountNumber]);
        }
        
        // Update existing savings accounts with new account numbers in SAV-BSS-C15-000x format
        $savingsAccounts = DB::table('savings_accounts')->orderBy('id')->get();
        foreach ($savingsAccounts as $index => $account) {
            $accountNumber = 'SAV-BSS-C15-' . str_pad($index + 1, 4, '0', STR_PAD_LEFT);
            DB::table('savings_accounts')
                ->where('id', $account->id)
                ->update(['account_number' => $accountNumber]);
        }
        
        // Add index for member_account_number
        DB::statement('ALTER TABLE members ADD INDEX idx_members_account_number (member_account_number)');
        
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        DB::statement('ALTER TABLE members DROP COLUMN member_account_number');
        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }
};