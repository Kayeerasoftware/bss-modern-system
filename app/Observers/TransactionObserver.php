<?php

namespace App\Observers;

use App\Models\Member;
use App\Models\Transaction;
use App\Services\Financial\MemberFinancialSyncService;

class TransactionObserver
{
    public function created(Transaction $transaction): void
    {
        $this->syncMemberFinancialState($transaction->member_id);
    }

    public function updated(Transaction $transaction): void
    {
        $this->syncMemberFinancialState($transaction->member_id);
        
        // If member_id changed, update both old and new member
        if ($transaction->isDirty('member_id')) {
            $this->syncMemberFinancialState($transaction->getOriginal('member_id'));
        }
    }

    public function deleted(Transaction $transaction): void
    {
        $this->syncMemberFinancialState($transaction->member_id);
    }

    public function restored(Transaction $transaction): void
    {
        $this->syncMemberFinancialState($transaction->member_id);
    }

    protected function syncMemberFinancialState(?int $memberId): void
    {
        if (!$memberId) {
            return;
        }

        $member = Member::find($memberId);
        if (!$member) {
            return;
        }

        app(MemberFinancialSyncService::class)->syncMember($member, true);
    }
}
