<?php

namespace App\Observers;

use App\Models\System\AuditLog;
use App\Services\AuditLogService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;

class GlobalAuditObserver
{
    public static function created(Model $model): void
    {
        if (!self::shouldAudit($model)) {
            return;
        }

        $after = self::sanitizeAttributes($model->getAttributes());
        unset($after['created_at'], $after['updated_at']);

        if ($after === []) {
            return;
        }

        AuditLogService::log(self::resolveActor(), 'create', self::buildDetails('created', $model), [
            'source' => 'model',
            'event' => 'created',
            'model' => class_basename($model),
            'model_class' => $model::class,
            'model_id' => $model->getKey(),
            'before' => [],
            'after' => $after,
            'fields' => array_keys($after),
        ]);
    }

    public static function updated(Model $model): void
    {
        if (!self::shouldAudit($model)) {
            return;
        }

        $changes = $model->getChanges();
        unset($changes['updated_at'], $changes['created_at']);

        if ($changes === []) {
            return;
        }

        $before = [];
        $after = [];

        foreach ($changes as $field => $newValue) {
            $before[$field] = $model->getOriginal($field);
            $after[$field] = $newValue;
        }

        $before = self::sanitizeAttributes($before);
        $after = self::sanitizeAttributes($after);

        AuditLogService::log(self::resolveActor(), 'update', self::buildDetails('updated', $model), [
            'source' => 'model',
            'event' => 'updated',
            'model' => class_basename($model),
            'model_class' => $model::class,
            'model_id' => $model->getKey(),
            'before' => $before,
            'after' => $after,
            'fields' => array_keys($after),
        ]);
    }

    public static function deleted(Model $model): void
    {
        if (!self::shouldAudit($model)) {
            return;
        }

        $before = self::sanitizeAttributes($model->getOriginal());
        unset($before['created_at'], $before['updated_at'], $before['deleted_at']);

        AuditLogService::log(self::resolveActor(), 'delete', self::buildDetails('deleted', $model), [
            'source' => 'model',
            'event' => 'deleted',
            'model' => class_basename($model),
            'model_class' => $model::class,
            'model_id' => $model->getKey(),
            'before' => $before,
            'after' => [],
            'fields' => array_keys($before),
        ]);
    }

    private static function shouldAudit(Model $model): bool
    {
        if (app()->runningInConsole()) {
            return false;
        }

        if ($model instanceof AuditLog) {
            return false;
        }

        return str_starts_with($model::class, 'App\\Models\\');
    }

    private static function resolveActor()
    {
        return Auth::guard('web')->user()
            ?? Auth::guard('sanctum')->user()
            ?? Auth::user();
    }

    private static function buildDetails(string $verb, Model $model): string
    {
        $name = class_basename($model);
        $id = $model->getKey();
        $label = method_exists($model, '__toString') ? (string) $model : '';

        $details = sprintf('User %s %s record #%s', $verb, $name, (string) $id);

        if ($label !== '' && $label !== $name) {
            $details .= ' (' . $label . ')';
        }

        return $details;
    }

    private static function sanitizeAttributes(array $attributes): array
    {
        $sensitive = [
            'password',
            'remember_token',
            'token',
            'secret',
            'api_key',
        ];

        foreach ($attributes as $key => $value) {
            $normalizedKey = strtolower((string) $key);
            if (in_array($normalizedKey, $sensitive, true)) {
                $attributes[$key] = '[REDACTED]';
            }
        }

        return $attributes;
    }
}
