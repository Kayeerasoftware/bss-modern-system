<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::transaction(function (): void {
            DB::table('loan_statuses')->updateOrInsert(
                ['name' => 'disbursed'],
                [
                    'display_name' => 'Disbursed',
                    'description' => 'Loan disbursed',
                    'color' => 'blue',
                    'is_active' => 1,
                ]
            );

            DB::table('transaction_types')->updateOrInsert(
                ['name' => 'loan_disbursement'],
                [
                    'display_name' => 'Loan Disbursement',
                    'description' => 'Loan disbursement to member',
                    'impact' => 'credit',
                    'affects_savings' => 1,
                    'affects_loan' => 1,
                    'is_active' => 1,
                ]
            );

            $typeId = DB::table('transaction_types')->where('name', 'loan_disbursement')->value('id');
            if ($typeId) {
                DB::table('transaction_categories')->updateOrInsert(
                    ['name' => 'loan_disbursement'],
                    [
                        'display_name' => 'Loan Disbursement',
                        'transaction_type_id' => $typeId,
                        'description' => 'Loan disbursement to member',
                        'is_active' => 1,
                    ]
                );
            }
        });
    }

    public function down(): void
    {
        DB::transaction(function (): void {
            DB::table('transaction_categories')->where('name', 'loan_disbursement')->delete();
            DB::table('transaction_types')->where('name', 'loan_disbursement')->delete();
            DB::table('loan_statuses')->where('name', 'disbursed')->delete();
        });
    }
};
