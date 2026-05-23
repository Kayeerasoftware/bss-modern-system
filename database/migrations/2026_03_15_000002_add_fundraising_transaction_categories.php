<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $depositTypeId = DB::table('transaction_types')->where('name', 'deposit')->value('id');
        $transferTypeId = DB::table('transaction_types')->where('name', 'transfer')->value('id');

        if ($depositTypeId) {
            $exists = DB::table('transaction_categories')->where('name', 'fundraising_deposit')->exists();
            if (!$exists) {
                DB::table('transaction_categories')->insert([
                    'name' => 'fundraising_deposit',
                    'display_name' => 'Fundraising Deposit',
                    'transaction_type_id' => $depositTypeId,
                    'description' => 'Fundraising contributions (deposit)',
                    'is_system' => 1,
                    'is_active' => 1,
                    'created_at' => now(),
                ]);
            }
        }

        if ($transferTypeId) {
            $exists = DB::table('transaction_categories')->where('name', 'fundraising_transfer')->exists();
            if (!$exists) {
                DB::table('transaction_categories')->insert([
                    'name' => 'fundraising_transfer',
                    'display_name' => 'Fundraising Transfer',
                    'transaction_type_id' => $transferTypeId,
                    'description' => 'Fundraising contributions (transfer)',
                    'is_system' => 1,
                    'is_active' => 1,
                    'created_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('transaction_categories')->whereIn('name', ['fundraising_deposit', 'fundraising_transfer'])->delete();
    }
};
