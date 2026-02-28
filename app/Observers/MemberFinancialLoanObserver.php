<?php

namespace App\Observers;

use App\Services\Financial\MemberFinancialSyncService;

class MemberFinancialLoanObserver
{
    private static bool $syncing = false;

    public function created($loan): void
    {
        $this->syncMembers($loan);
    }

    public function updated($loan): void
    {
        if (!$this->hasRelevantChanges($loan)) {
            return;
        }

        $originalMemberId = (string) ($loan->getOriginal('member_id') ?? '');
        $this->syncMembers($loan, $originalMemberId);
    }

    public function deleted($loan): void
    {
        $originalMemberId = (string) ($loan->getOriginal('member_id') ?? '');
        $this->syncMembers($loan, $originalMemberId);
    }

    public function restored($loan): void
    {
        $this->syncMembers($loan);
    }

    public function forceDeleted($loan): void
    {
        $originalMemberId = (string) ($loan->getOriginal('member_id') ?? '');
        $this->syncMembers($loan, $originalMemberId);
    }

    private function syncMembers($loan, ?string $originalMemberId = null): void
    {
        if (self::$syncing) {
            return;
        }

        self::$syncing = true;

        try {
            $service = app(MemberFinancialSyncService::class);

            $currentMemberId = (string) ($loan->member_id ?? '');
            if ($currentMemberId !== '') {
                $service->syncByMemberId($currentMemberId);
            }

            if ($originalMemberId && $originalMemberId !== $currentMemberId) {
                $service->syncByMemberId($originalMemberId);
            }
        } finally {
            self::$syncing = false;
        }
    }

    private function hasRelevantChanges($loan): bool
    {
        $watchedColumns = [
            'member_id',
            'amount',
            'paid_amount',
            'amount_paid',
            'status',
            'deleted_at',
        ];

        foreach ($watchedColumns as $column) {
            if ($loan->wasChanged($column)) {
                return true;
            }
        }

        return false;
    }
}
