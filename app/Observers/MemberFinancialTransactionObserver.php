<?php

namespace App\Observers;

use App\Services\Financial\MemberFinancialSyncService;

class MemberFinancialTransactionObserver
{
    private static bool $syncing = false;

    public function created($transaction): void
    {
        $this->syncMembers($transaction);
    }

    public function updated($transaction): void
    {
        if (!$this->hasRelevantChanges($transaction)) {
            return;
        }

        $originalMemberId = (string) ($transaction->getOriginal('member_id') ?? '');
        $this->syncMembers($transaction, $originalMemberId);
    }

    public function deleted($transaction): void
    {
        $originalMemberId = (string) ($transaction->getOriginal('member_id') ?? '');
        $this->syncMembers($transaction, $originalMemberId);
    }

    public function restored($transaction): void
    {
        $this->syncMembers($transaction);
    }

    public function forceDeleted($transaction): void
    {
        $originalMemberId = (string) ($transaction->getOriginal('member_id') ?? '');
        $this->syncMembers($transaction, $originalMemberId);
    }

    private function syncMembers($transaction, ?string $originalMemberId = null): void
    {
        if (self::$syncing) {
            return;
        }

        self::$syncing = true;

        try {
            $service = app(MemberFinancialSyncService::class);

            $currentMemberId = (string) ($transaction->member_id ?? '');
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

    private function hasRelevantChanges($transaction): bool
    {
        $watchedColumns = [
            'member_id',
            'type',
            'status',
            'amount',
            'net_amount',
            'balance_before',
            'balance_after',
            'deleted_at',
        ];

        foreach ($watchedColumns as $column) {
            if ($transaction->wasChanged($column)) {
                return true;
            }
        }

        return false;
    }
}
