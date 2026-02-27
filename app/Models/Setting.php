<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    protected $fillable = ['key', 'value'];
    private static array $runtimeCache = [];

    public static function get($key, $default = null)
    {
        if (array_key_exists($key, self::$runtimeCache)) {
            return self::$runtimeCache[$key];
        }

        $resolved = Cache::remember(
            'setting:'.$key,
            now()->addMinutes(5),
            static function () use ($key, $default) {
                $setting = self::query()->where('key', $key)->first();

                if (!$setting) {
                    return $default;
                }

                // Try to decode JSON, return original value if not JSON
                $decoded = json_decode($setting->value, true);
                return json_last_error() === JSON_ERROR_NONE ? $decoded : $setting->value;
            }
        );

        self::$runtimeCache[$key] = $resolved;

        return $resolved;
    }

    public static function set($key, $value)
    {
        $storedValue = is_array($value) || is_object($value)
            ? json_encode($value)
            : $value;

        $result = self::updateOrCreate(['key' => $key], ['value' => $storedValue]);

        Cache::forget('setting:'.$key);
        Cache::forget('setting:all');
        unset(self::$runtimeCache[$key], self::$runtimeCache['__all']);

        return $result;
    }

    public static function all_settings()
    {
        if (array_key_exists('__all', self::$runtimeCache)) {
            return self::$runtimeCache['__all'];
        }

        $resolved = Cache::remember(
            'setting:all',
            now()->addMinutes(5),
            static function () {
                $settings = self::query()->get();
                $result = [];

                foreach ($settings as $setting) {
                    $decoded = json_decode($setting->value, true);
                    $result[$setting->key] = json_last_error() === JSON_ERROR_NONE ? $decoded : $setting->value;
                }

                return $result;
            }
        );

        self::$runtimeCache['__all'] = $resolved;

        return $resolved;
    }
}
