<?php

namespace App\Services;

use App\Jobs\WriteAuditLog;
use App\Models\System\AuditLog;
use App\Models\AuditActionType;
use App\Models\EntityType;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Throwable;

class AuditLogService
{
    /**
     * Persist an audit log entry.
     *
     * @param  Authenticatable|string|null  $actor
     */
    public static function log($actor, string $action, string $details, array $changes = []): void
    {
        try {
            $sanitizedChanges = self::sanitizeChanges($changes);

            if (config('audit.queue_enabled', true) && config('queue.default') !== 'sync') {
                WriteAuditLog::dispatch($actor, $action, $details, $sanitizedChanges)->onQueue('default');
                return;
            }

            $actionTypeId = self::resolveActionTypeId($action);
            $entityTypeId = self::resolveEntityTypeId($sanitizedChanges, $actor);

            $userId = null;
            $memberId = null;
            if ($actor instanceof Authenticatable) {
                $userId = $actor instanceof User ? $actor->id : null;
                $memberId = $actor instanceof User ? $actor->member?->id : null;
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
                'entity_id' => $sanitizedChanges['entity_id'] ?? null,
                'entity_identifier' => $sanitizedChanges['entity_identifier'] ?? null,
                'description' => $details,
                'details' => $sanitizedChanges,
            ]);
        } catch (Throwable $e) {
            // Never break user flow due to audit logging failure.
            report($e);
        }
    }

    /**
     * @param  Authenticatable|string|null  $actor
     */
    private static function resolveActorName($actor): string
    {
        if (is_string($actor) && $actor !== '') {
            return $actor;
        }

        if ($actor instanceof Authenticatable) {
            return (string) ($actor->name ?? $actor->email ?? 'Unknown User');
        }

        return 'System';
    }

    private static function resolveActionTypeId(string $action): int
    {
        $normalized = strtolower(trim($action));
        $actionId = AuditActionType::query()->where('name', $normalized)->value('id');

        if ($actionId) {
            return (int) $actionId;
        }

        return (int) (AuditActionType::query()->where('name', 'update')->value('id') ?? 1);
    }

    private static function resolveEntityTypeId(array $changes, $actor): int
    {
        if (!empty($changes['entity_type'])) {
            $id = EntityType::query()->where('name', $changes['entity_type'])->value('id');
            if ($id) {
                return (int) $id;
            }
        }

        if ($actor instanceof User) {
            $id = EntityType::query()->where('name', 'user')->value('id');
            if ($id) {
                return (int) $id;
            }
        }

        return (int) (EntityType::query()->value('id') ?? 1);
    }

    private static function sanitizeChanges(array $changes): array
    {
        $sensitiveKeys = [
            'password',
            'password_confirmation',
            'current_password',
            'token',
            '_token',
            'api_key',
            'secret',
        ];

        $normalized = [];
        foreach ($changes as $key => $value) {
            if ($value instanceof UploadedFile) {
                // Do not call getSize()/path-based APIs here; temp upload files may already be moved/removed.
                $normalized[$key] = [
                    'uploaded_file' => $value->getClientOriginalName(),
                    'mime' => $value->getClientMimeType(),
                    'client_size' => $value->getClientSize(),
                ];
                continue;
            }

            if (is_array($value)) {
                $normalized[$key] = self::sanitizeChanges($value);
                continue;
            }

            if (in_array(strtolower((string) $key), $sensitiveKeys, true)) {
                $normalized[$key] = '[REDACTED]';
                continue;
            }

            $normalized[$key] = $value;
        }

        // Keep payload reasonable in size for admin viewing.
        return Arr::only($normalized, array_slice(array_keys($normalized), 0, 50));
    }
}
