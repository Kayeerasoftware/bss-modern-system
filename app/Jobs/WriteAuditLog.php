<?php

namespace App\Jobs;

use App\Models\System\AuditLog;
use App\Models\AuditActionType;
use App\Models\EntityType;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class WriteAuditLog implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private readonly mixed $actor,
        private readonly string $action,
        private readonly string $details,
        private readonly array $changes = []
    ) {
    }

    public function handle(): void
    {
        $actionTypeId = AuditActionType::query()->where('name', strtolower($this->action))->value('id')
            ?? AuditActionType::query()->where('name', 'update')->value('id')
            ?? 1;

        $entityTypeId = null;
        if (!empty($this->changes['entity_type'])) {
            $entityTypeId = EntityType::query()->where('name', $this->changes['entity_type'])->value('id');
        }
        if (!$entityTypeId && $this->actor instanceof User) {
            $entityTypeId = EntityType::query()->where('name', 'user')->value('id');
        }
        $entityTypeId = $entityTypeId ?: (EntityType::query()->value('id') ?? 1);

        $userId = null;
        $memberId = null;
        if ($this->actor instanceof Authenticatable) {
            $userId = $this->actor instanceof User ? $this->actor->id : null;
            $memberId = $this->actor instanceof User ? $this->actor->member?->id : null;
        }

        AuditLog::create([
            'log_number' => 'AUD-' . now()->format('Ymd') . '-' . str_pad((string) random_int(1, 9999), 4, '0', STR_PAD_LEFT),
            'user_id' => $userId,
            'member_id' => $memberId,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'session_id' => request()->session()->getId(),
            'action_type_id' => $actionTypeId,
            'entity_type_id' => $entityTypeId,
            'entity_id' => $this->changes['entity_id'] ?? null,
            'entity_identifier' => $this->changes['entity_identifier'] ?? null,
            'description' => $this->details,
            'details' => $this->changes,
        ]);
    }
}
