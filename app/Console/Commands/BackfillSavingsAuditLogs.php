<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class BackfillSavingsAuditLogs extends Command
{
    protected $signature = 'savings:backfill-audit {--limit=0 : Limit number of logs} {--dry-run : Show counts without writing}';

    protected $description = 'Backfill audit logs for savings-related transactions missing audit entries.';

    public function handle(): int
    {
        $limit = (int) $this->option('limit');
        $dryRun = (bool) $this->option('dry-run');

        $transactionEntityId = DB::table('entity_types')->where('name', 'transaction')->value('id');
        if (!$transactionEntityId) {
            $this->error('Missing entity_types entry for transaction.');
            return self::FAILURE;
        }

        $actionTypeId = DB::table('audit_action_types')->where('name', 'update')->value('id')
            ?? DB::table('audit_action_types')->where('name', 'create')->value('id');
        if (!$actionTypeId) {
            $this->error('Missing audit_action_types entries.');
            return self::FAILURE;
        }

        $categoryNames = ['savings_deposit', 'savings_withdrawal', 'transfer_in', 'transfer_out'];

        $missingQuery = DB::table('transactions as t')
            ->join('transaction_categories as tc', 't.category_id', '=', 'tc.id')
            ->leftJoin('audit_logs as al', function ($join) use ($transactionEntityId) {
                $join->on('al.entity_id', '=', 't.id')
                    ->where('al.entity_type_id', '=', $transactionEntityId);
            })
            ->whereIn('tc.name', $categoryNames)
            ->whereNull('al.id')
            ->select(
                't.id',
                't.member_id',
                't.processed_by',
                't.transaction_number',
                't.reference_number',
                't.amount',
                't.net_amount',
                't.transaction_date',
                't.created_at',
                'tc.name as category_name',
                'tc.display_name as category_display'
            );

        if ($limit > 0) {
            $missingQuery->limit($limit);
        }

        $missingCount = (int) (clone $missingQuery)->count();
        $this->info('Missing savings audit logs: ' . number_format($missingCount));

        if ($missingCount === 0) {
            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->line('Dry run enabled. No audit logs created.');
            return self::SUCCESS;
        }

        $inserted = 0;
        $missingQuery->orderBy('t.id')->chunkById(200, function ($rows) use (&$inserted, $actionTypeId, $transactionEntityId): void {
            foreach ($rows as $row) {
                $logNumber = 'AUD-' . now()->format('Ymd') . '-TX' . $row->id;
                $createdAt = $row->transaction_date ?? $row->created_at ?? now();
                $amount = (float) ($row->net_amount ?? $row->amount ?? 0);

                DB::table('audit_logs')->insert([
                    'log_number' => $logNumber,
                    'user_id' => $row->processed_by,
                    'member_id' => $row->member_id,
                    'action_type_id' => $actionTypeId,
                    'entity_type_id' => $transactionEntityId,
                    'entity_id' => $row->id,
                    'entity_identifier' => $row->transaction_number ?? $row->reference_number,
                    'description' => 'Backfilled savings transaction log',
                    'details' => json_encode([
                        'category' => $row->category_name,
                        'display_name' => $row->category_display,
                        'amount' => $amount,
                    ]),
                    'request_method' => null,
                    'request_url' => null,
                    'response_status' => null,
                    'execution_time_ms' => null,
                    'created_at' => $createdAt,
                ]);
                $inserted++;
            }
        });

        $this->info('Audit logs created: ' . number_format($inserted));
        return self::SUCCESS;
    }
}
