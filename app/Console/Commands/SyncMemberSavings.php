<?php

namespace App\Console\Commands;

use App\Models\Member;
use App\Services\Financial\MemberFinancialSyncService;
use Illuminate\Console\Command;

class SyncMemberSavings extends Command
{
    protected $signature = 'members:sync-savings {--member_id=}';
    protected $description = 'Sync member savings from transactions to members table';

    public function handle(MemberFinancialSyncService $syncService)
    {
        $memberId = $this->option('member_id');

        if ($memberId) {
            $member = Member::find($memberId);
            if (!$member) {
                $this->error("Member {$memberId} not found.");
                return 1;
            }

            $syncService->syncMember($member, true);
            $this->info("Successfully synced financial summary for member {$memberId}.");
            return 0;
        }

        $this->info('Syncing member savings from transactions...');
        $report = $syncService->syncAll(true);
        $this->info("Successfully synced {$report['processed']} members. Changed: {$report['changed']}");

        return 0;
    }
}
