<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use App\Services\System\AccountNumberService;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        if (Schema::hasColumn('members', 'savings')) {
            DB::transaction(function (): void {
                $planId = $this->resolveSavingsPlanId();
                $now = now();

                $members = DB::table('members')
                    ->select('id', 'join_date', 'created_at', 'savings')
                    ->whereNotNull('savings')
                    ->orderBy('id')
                    ->get();

                foreach ($members as $member) {
                    $legacyBalance = round((float) ($member->savings ?? 0), 2);
                    if ($legacyBalance <= 0) {
                        continue;
                    }

                    $account = DB::table('savings_accounts')
                        ->where('member_id', $member->id)
                        ->orderBy('id')
                        ->first();

                    $openingDate = $member->join_date ?? $member->created_at ?? $now->toDateString();
                    $openingDate = $openingDate instanceof \DateTimeInterface
                        ? $openingDate->format('Y-m-d')
                        : (string) $openingDate;

                    if ($account) {
                        $hasSavingsHistory = Schema::hasTable('savings_transactions')
                            ? DB::table('savings_transactions')
                                ->where('savings_account_id', $account->id)
                                ->exists()
                            : false;

                        $currentBalance = round((float) ($account->current_balance ?? 0), 2);
                        if (!$hasSavingsHistory && abs($currentBalance) < 0.01) {
                            DB::table('savings_accounts')
                                ->where('id', $account->id)
                                ->update([
                                    'opening_balance' => $legacyBalance,
                                    'current_balance' => $legacyBalance,
                                    'available_balance' => $legacyBalance,
                                    'opening_date' => $openingDate,
                                    'status' => $legacyBalance > 0 ? 'active' : 'dormant',
                                    'updated_at' => $now,
                                ]);
                        }

                        continue;
                    }

                    DB::table('savings_accounts')->insert([
                        'account_number' => $this->buildAccountNumber(),
                        'member_id' => $member->id,
                        'plan_id' => $planId,
                        'account_name' => 'Member Savings',
                        'opening_balance' => $legacyBalance,
                        'current_balance' => $legacyBalance,
                        'available_balance' => $legacyBalance,
                        'opening_date' => $openingDate,
                        'status' => $legacyBalance > 0 ? 'active' : 'dormant',
                        'created_at' => $now,
                        'updated_at' => $now,
                    ]);
                }
            });

            Schema::table('members', function (Blueprint $table): void {
                $table->dropColumn('savings');
            });
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    public function down(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        if (!Schema::hasColumn('members', 'savings')) {
            Schema::table('members', function (Blueprint $table): void {
                $table->decimal('savings', 15, 2)->default(0)->after('membership_status');
            });
        }

        if (Schema::hasTable('savings_accounts')) {
            DB::statement(<<<'SQL'
UPDATE members m
LEFT JOIN (
    SELECT member_id, COALESCE(SUM(current_balance), 0) AS balance
    FROM savings_accounts
    GROUP BY member_id
) sa ON sa.member_id = m.id
SET m.savings = COALESCE(sa.balance, 0)
SQL);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function resolveSavingsPlanId(): int
    {
        $planId = DB::table('savings_plans')->where('is_active', 1)->value('id')
            ?? DB::table('savings_plans')->value('id');

        if ($planId) {
            return (int) $planId;
        }

        $planTypeId = DB::table('savings_plan_types')->where('is_active', 1)->value('id')
            ?? DB::table('savings_plan_types')->value('id');

        if (!$planTypeId) {
            $planTypeId = DB::table('savings_plan_types')->insertGetId([
                'name' => 'Default Savings',
                'description' => 'Auto-created savings plan type.',
                'min_balance' => 0,
                'interest_rate' => 0,
                'interest_calculation' => 'monthly',
                'withdrawal_fee_percentage' => 0,
                'withdrawal_fee_fixed' => 0,
                'is_taxable' => 0,
                'is_active' => 1,
                'created_at' => now(),
            ]);
        }

        return (int) DB::table('savings_plans')->insertGetId([
            'plan_type_id' => $planTypeId,
            'name' => 'Default Savings Plan',
            'description' => 'Auto-created savings plan.',
            'minimum_balance' => 0,
            'interest_rate' => 0,
            'interest_calculation' => 'monthly',
            'interest_payout' => 'compound',
            'monthly_fee' => 0,
            'withdrawal_fee_percentage' => 0,
            'withdrawal_fee_fixed' => 0,
            'early_withdrawal_penalty' => 0,
            'min_deposit' => 0,
            'max_deposit' => null,
            'min_withdrawal' => null,
            'max_withdrawal' => null,
            'withdrawal_limit_period' => null,
            'withdrawal_limit_count' => null,
            'min_duration_months' => null,
            'max_duration_months' => null,
            'is_taxable' => 0,
            'tax_rate' => 0,
            'allows_overdraft' => 0,
            'overdraft_limit' => null,
            'overdraft_interest_rate' => null,
            'is_active' => 1,
            'created_at' => now(),
        ]);
    }

    private function buildAccountNumber(): string
    {
        return AccountNumberService::generateSavingsAccountNumber();
    }
};
