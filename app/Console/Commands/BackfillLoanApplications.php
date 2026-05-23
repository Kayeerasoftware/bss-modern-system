<?php

namespace App\Console\Commands;

use App\Models\Loan;
use App\Models\LoanApplication;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillLoanApplications extends Command
{
    protected $signature = 'loans:backfill-applications {--dry-run : Show what would change without writing}';
    protected $description = 'Create missing loan_applications rows for loans without an application_id.';

    public function handle(): int
    {
        $dryRun = (bool) $this->option('dry-run');

        $loans = Loan::query()
            ->whereNull('application_id')
            ->orderBy('id')
            ->get();

        if ($loans->isEmpty()) {
            $this->info('No loans missing applications.');
            return Command::SUCCESS;
        }

        $this->info(sprintf('Found %d loan(s) without applications.', $loans->count()));

        $updated = 0;
        foreach ($loans as $loan) {
            $payload = [
                'member_id' => $loan->member_id,
                'loan_type_id' => $loan->loan_type_id,
                'requested_amount' => $loan->principal_amount ?? 0,
                'approved_amount' => in_array($loan->status, ['approved', 'disbursed', 'completed'], true)
                    ? $loan->principal_amount
                    : null,
                'requested_tenure_months' => $loan->repayment_months ?? 1,
                'approved_tenure_months' => in_array($loan->status, ['approved', 'disbursed', 'completed'], true)
                    ? $loan->repayment_months
                    : null,
                'purpose' => $loan->notes ?? 'Loan created before applications were tracked.',
                'status_id' => $loan->status_id,
                'submission_date' => $loan->application_date ?? $loan->created_at ?? now(),
                'decision_date' => $loan->approval_date ?? null,
                'converted_to_loan_id' => $loan->id,
            ];

            if ($dryRun) {
                $this->line(sprintf(
                    'Would create application for loan #%s (member %d, amount %s).',
                    $loan->loan_number ?? $loan->id,
                    $loan->member_id,
                    number_format((float) $loan->principal_amount, 2)
                ));
                continue;
            }

            DB::transaction(function () use ($loan, $payload, &$updated): void {
                $application = LoanApplication::create($payload);
                $loan->update(['application_id' => $application->id]);
                $updated++;
            });
        }

        if ($dryRun) {
            $this->info('Dry run complete. No changes were written.');
            return Command::SUCCESS;
        }

        $this->info(sprintf('Backfilled %d loan application(s).', $updated));
        return Command::SUCCESS;
    }
}
