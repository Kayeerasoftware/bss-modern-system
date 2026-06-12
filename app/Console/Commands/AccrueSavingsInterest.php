<?php

namespace App\Console\Commands;

use App\Models\SavingsAccount;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class AccrueSavingsInterest extends Command
{
    protected $signature = 'savings:accrue-interest {--date=}';
    protected $description = 'Accrue savings interest based on system settings interest rate.';

    public function handle(): int
    {
        $rate = (float) setting('interest_rate', 0);
        if ($rate <= 0) {
            $this->info('Savings interest rate is 0. Nothing to accrue.');
            return self::SUCCESS;
        }

        $targetDate = $this->option('date')
            ? Carbon::parse($this->option('date'))->startOfDay()
            : now()->startOfDay();

        $accounts = SavingsAccount::query()
            ->where('status', 'active')
            ->get(['id', 'current_balance', 'opening_date', 'created_at', 'last_interest_calculation', 'accrued_interest']);

        $accruedCount = 0;
        $totalAccrued = 0.0;

        DB::transaction(function () use ($accounts, $rate, $targetDate, &$accruedCount, &$totalAccrued) {
            foreach ($accounts as $account) {
                $lastCalc = $account->last_interest_calculation
                    ? Carbon::parse($account->last_interest_calculation)->startOfDay()
                    : ($account->opening_date
                        ? Carbon::parse($account->opening_date)->startOfDay()
                        : Carbon::parse($account->created_at)->startOfDay());

                $periodStart = $lastCalc->copy()->addDay();
                $periodEnd = $targetDate->copy();

                if ($periodStart->gt($periodEnd)) {
                    continue;
                }

                $days = $periodStart->diffInDays($periodEnd) + 1;
                $averageBalance = (float) ($account->current_balance ?? 0);
                $interestAmount = round(($averageBalance * ($rate / 100)) * ($days / 365), 2);

                if ($interestAmount <= 0) {
                    $account->last_interest_calculation = $periodEnd->toDateString();
                    $account->save();
                    continue;
                }

                DB::table('savings_interest_accruals')->insert([
                    'savings_account_id' => $account->id,
                    'period_start' => $periodStart->toDateString(),
                    'period_end' => $periodEnd->toDateString(),
                    'average_balance' => $averageBalance,
                    'interest_rate' => $rate,
                    'interest_amount' => $interestAmount,
                    'tax_amount' => 0,
                    'paid_transaction_id' => null,
                    'paid_at' => null,
                    'status' => 'accrued',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                $account->last_interest_calculation = $periodEnd->toDateString();
                $account->accrued_interest = (float) ($account->accrued_interest ?? 0) + $interestAmount;
                $account->save();

                $accruedCount++;
                $totalAccrued += $interestAmount;
            }
        });

        $this->info("Accrued interest for {$accruedCount} accounts. Total accrued: {$totalAccrued}");

        return self::SUCCESS;
    }
}
