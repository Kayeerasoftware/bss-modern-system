<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class Setting extends Model
{
    protected $table = 'settings';
    protected $primaryKey = 'id';
    protected $fillable = ['setting_key', 'setting_value', 'setting_type', 'category', 'display_name', 'description', 'is_public', 'sort_order'];
    private static array $runtimeCache = [];

    public static function get($key, $default = null)
    {
        $columns = self::settingColumns();
        if ($columns === null) {
            return $default;
        }

        if (array_key_exists($key, self::$runtimeCache)) {
            return self::$runtimeCache[$key];
        }

        $resolved = Cache::remember(
            'setting:'.$key,
            now()->addMinutes(5),
            static function () use ($key, $default, $columns) {
                $setting = self::query()
                    ->where($columns['key'], $key)
                    ->first();

                if (!$setting) {
                    return $default;
                }

                // Try to decode JSON, return original value if not JSON
                $rawValue = $setting->{$columns['value']} ?? null;
                $decoded = json_decode((string) $rawValue, true);
                return json_last_error() === JSON_ERROR_NONE ? $decoded : $rawValue;
            }
        );

        self::$runtimeCache[$key] = $resolved;

        return $resolved;
    }

    public static function set($key, $value)
    {
        $columns = self::settingColumns();
        if ($columns === null) {
            return null;
        }

        $storedValue = is_array($value) || is_object($value)
            ? json_encode($value)
            : $value;

        $result = self::updateOrCreate(
            [$columns['key'] => $key],
            [$columns['value'] => $storedValue]
        );

        Cache::forget('setting:'.$key);
        Cache::forget('setting:all');
        unset(self::$runtimeCache[$key], self::$runtimeCache['__all']);

        return $result;
    }

    public static function all_settings()
    {
        $columns = self::settingColumns();
        if ($columns === null) {
            return [];
        }

        if (array_key_exists('__all', self::$runtimeCache)) {
            return self::$runtimeCache['__all'];
        }

        $resolved = Cache::remember(
            'setting:all',
            now()->addMinutes(5),
            static function () use ($columns) {
                $settings = self::query()->get();
                $result = [];

                foreach ($settings as $setting) {
                    $key = $setting->{$columns['key']} ?? null;
                    if ($key === null) {
                        continue;
                    }

                    $rawValue = $setting->{$columns['value']} ?? null;
                    $decoded = json_decode((string) $rawValue, true);
                    $result[$key] = json_last_error() === JSON_ERROR_NONE ? $decoded : $rawValue;
                }

                return $result;
            }
        );

        self::$runtimeCache['__all'] = $resolved;

        return $resolved;
    }

    private static function settingColumns(): ?array
    {
        if (!Schema::hasTable('settings')) {
            return null;
        }

        $keyCandidates = ['setting_key', 'key', 'name', 'setting_name'];
        $valueCandidates = ['setting_value', 'value', 'setting', 'option_value'];

        $keyColumn = collect($keyCandidates)->first(static fn (string $column): bool => Schema::hasColumn('settings', $column));
        $valueColumn = collect($valueCandidates)->first(static fn (string $column): bool => Schema::hasColumn('settings', $column));

        if ($keyColumn === null || $valueColumn === null) {
            return null;
        }

        return [
            'key' => $keyColumn,
            'value' => $valueColumn,
        ];
    }
}
