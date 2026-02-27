<?php

namespace App\Jobs;

use App\Models\System\AuditLog;
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
        private readonly string $user,
        private readonly string $action,
        private readonly string $details,
        private readonly array $changes = []
    ) {
    }

    public function handle(): void
    {
        AuditLog::create([
            'user' => $this->user,
            'action' => $this->action,
            'details' => $this->details,
            'changes' => $this->changes,
            'timestamp' => now(),
        ]);
    }
}
