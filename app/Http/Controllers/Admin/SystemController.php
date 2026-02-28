<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SystemController extends Controller
{
    public function settings()
    {
        $settings = \App\Models\Setting::all_settings();
        return view('admin.system.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        foreach ($request->except('_token') as $key => $value) {
            // Handle array values (like access permissions)
            if (is_array($value)) {
                $value = json_encode($value);
            }
            \App\Models\Setting::set($key, $value);
        }

        return redirect()->route('admin.system.settings')->with('success', 'Settings updated successfully');
    }

    public function auditLogs(Request $request)
    {
        $settings = \App\Models\Setting::all_settings();
        
        $query = \App\Models\System\AuditLog::query();
        
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('user', 'like', '%' . $request->search . '%')
                  ->orWhere('details', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('user')) {
            $query->where('user', 'like', '%' . $request->user . '%');
        }
        
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }
        
        if ($request->filled('sort')) {
            $query->orderBy('created_at', $request->sort == 'oldest' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $perPage = $request->get('per_page', 20);
        $logs = $query->paginate($perPage)->appends($request->except('page'));
        $logsData = $logs->getCollection()
            ->map(fn ($log) => $this->formatAuditLog($log))
            ->values()
            ->all();
        
        return view('admin.system.audit-logs', compact('settings', 'logs', 'logsData'));
    }

    public function backups()
    {
        return view('admin.system.backups');
    }

    public function createBackup()
    {
        // Implementation
    }

    public function downloadBackup($id)
    {
        // Implementation
    }

    public function health()
    {
        return redirect()->route('admin.system-health.index');
    }

    private function formatAuditLog($log): array
    {
        $changes = is_array($log->changes) ? $log->changes : [];
        $statusCode = (int) ($changes['status'] ?? 0);
        [$statusText, $statusClass] = $this->resolveStatusMeta($statusCode);
        $action = strtolower((string) $log->action);

        $user = User::query()
            ->where('name', $log->user)
            ->latest('id')
            ->first(['name', 'email', 'phone', 'role', 'profile_picture']);

        $payloadChanges = Arr::get($changes, 'payload', []);
        $queryChanges = Arr::get($changes, 'query', []);
        $changeItems = $this->buildChangeItems($payloadChanges, $queryChanges);

        return [
            'id' => (string) $log->id,
            'timestamp' => optional($log->created_at)->format('Y-m-d H:i:s') ?? (string) $log->timestamp,
            'user' => (string) $log->user,
            'userRole' => $user?->role ? ucfirst((string) $user->role) : 'System User',
            'userEmail' => $user?->email ?? 'N/A',
            'userPhone' => $user?->phone ?? 'N/A',
            'userPhoto' => $user?->profile_picture_url ?? ('https://ui-avatars.com/api/?name=' . urlencode((string) $log->user) . '&background=3b82f6&color=fff'),
            'action' => ucfirst((string) $log->action),
            'module' => $this->resolveModule($changes),
            'details' => (string) $log->details,
            'description' => $this->buildDescription((string) $log->details, $payloadChanges, $queryChanges, $statusCode),
            'ip' => (string) ($changes['ip'] ?? 'N/A'),
            'location' => 'N/A',
            'userAgent' => (string) ($changes['user_agent'] ?? 'N/A'),
            'browser' => $this->browserFromUserAgent((string) ($changes['user_agent'] ?? '')),
            'device' => $this->deviceFromUserAgent((string) ($changes['user_agent'] ?? '')),
            'platform' => $this->platformFromUserAgent((string) ($changes['user_agent'] ?? '')),
            'userColor' => 'bg-blue-600',
            'actionBadge' => $this->actionBadge($action),
            'sessionId' => 'N/A',
            'requestId' => 'N/A',
            'duration' => 'N/A',
            'status' => $statusText,
            'statusCode' => $statusCode ?: null,
            'statusClass' => $statusClass,
            'changes' => $changes,
            'payloadChanges' => $payloadChanges,
            'queryChanges' => $queryChanges,
            'changeItems' => $changeItems,
        ];
    }

    private function buildChangeItems($payloadChanges, $queryChanges): array
    {
        $items = [];

        if (is_array($payloadChanges)) {
            foreach ($payloadChanges as $field => $value) {
                $items[] = [
                    'source' => 'payload',
                    'field' => (string) $field,
                    'value' => is_scalar($value) || $value === null ? $value : json_encode($value, JSON_UNESCAPED_UNICODE),
                ];
            }
        }

        if (is_array($queryChanges)) {
            foreach ($queryChanges as $field => $value) {
                $items[] = [
                    'source' => 'query',
                    'field' => (string) $field,
                    'value' => is_scalar($value) || $value === null ? $value : json_encode($value, JSON_UNESCAPED_UNICODE),
                ];
            }
        }

        return array_slice($items, 0, 50);
    }

    private function resolveStatusMeta(int $statusCode): array
    {
        if ($statusCode >= 500) {
            return ['Failure', 'bg-red-100 text-red-700'];
        }
        if ($statusCode >= 400) {
            return ['Failed Request', 'bg-rose-100 text-rose-700'];
        }
        if ($statusCode >= 300) {
            return ['Redirected', 'bg-yellow-100 text-yellow-700'];
        }
        if ($statusCode >= 200) {
            return ['Success', 'bg-green-100 text-green-700'];
        }

        return ['Unknown', 'bg-gray-100 text-gray-700'];
    }

    private function resolveModule(array $changes): string
    {
        $route = (string) ($changes['route'] ?? '');
        $path = (string) ($changes['path'] ?? '');
        $target = $route !== '' ? $route : $path;

        if ($target === '') {
            return 'System';
        }

        $parts = explode('.', $target);
        if (count($parts) > 1) {
            return ucfirst((string) $parts[1]);
        }

        $segments = array_values(array_filter(explode('/', trim($target, '/'))));
        return isset($segments[1]) ? ucfirst((string) $segments[1]) : ucfirst((string) $segments[0]);
    }

    private function actionBadge(string $action): string
    {
        return match ($action) {
            'create', 'login' => 'bg-green-100 text-green-700',
            'update', 'role_switch' => 'bg-blue-100 text-blue-700',
            'delete' => 'bg-red-100 text-red-700',
            'download' => 'bg-purple-100 text-purple-700',
            default => 'bg-gray-100 text-gray-700',
        };
    }

    private function buildDescription(string $details, $payload, $query, int $statusCode): string
    {
        $description = $details;
        $parts = [];

        if (is_array($payload) && $payload !== []) {
            $parts[] = 'Payload fields changed: ' . implode(', ', array_keys($payload));
        }
        if (is_array($query) && $query !== []) {
            $parts[] = 'Query params: ' . implode(', ', array_keys($query));
        }
        if ($statusCode > 0) {
            $parts[] = 'HTTP status: ' . $statusCode;
        }

        if ($parts !== []) {
            $description .= ' | ' . implode(' | ', $parts);
        }

        return $description;
    }

    private function browserFromUserAgent(string $ua): string
    {
        if ($ua === '') {
            return 'N/A';
        }

        if (Str::contains($ua, 'Edg/')) return 'Edge';
        if (Str::contains($ua, 'Chrome/')) return 'Chrome';
        if (Str::contains($ua, 'Firefox/')) return 'Firefox';
        if (Str::contains($ua, 'Safari/') && !Str::contains($ua, 'Chrome/')) return 'Safari';

        return 'Browser';
    }

    private function platformFromUserAgent(string $ua): string
    {
        if ($ua === '') {
            return 'N/A';
        }

        if (Str::contains($ua, 'Windows')) return 'Windows';
        if (Str::contains($ua, 'Macintosh')) return 'macOS';
        if (Str::contains($ua, 'Android')) return 'Android';
        if (Str::contains($ua, 'iPhone') || Str::contains($ua, 'iPad')) return 'iOS';
        if (Str::contains($ua, 'Linux')) return 'Linux';

        return 'Unknown';
    }

    private function deviceFromUserAgent(string $ua): string
    {
        if ($ua === '') {
            return 'N/A';
        }

        if (Str::contains($ua, ['Mobile', 'Android', 'iPhone'])) return 'Mobile';
        if (Str::contains($ua, 'iPad')) return 'Tablet';

        return 'Desktop';
    }
}
