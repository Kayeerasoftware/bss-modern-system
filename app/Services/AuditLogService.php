<?php

namespace App\Services;

use App\Models\System\AuditLog;
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
            $userName = self::resolveActorName($actor);

            AuditLog::create([
                'user' => $userName,
                'action' => $action,
                'details' => $details,
                'changes' => self::sanitizeChanges($changes),
                'timestamp' => now(),
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
