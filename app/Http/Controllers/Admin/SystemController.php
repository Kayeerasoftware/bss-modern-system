<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoanSetting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
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
        $data = $request->except('_token');

        $data['is_loan_available'] = $request->has('is_loan_available') ? 1 : 0;
        $data['email_notifications'] = (int) $request->input('email_notifications', 0);
        $data['sms_notifications'] = (int) $request->input('sms_notifications', 0);

        foreach ($data as $key => $value) {
            // Handle array values (like access permissions)
            if (is_array($value)) {
                $value = json_encode($value);
            }
            \App\Models\Setting::set($key, $value);
        }

        $loanKeys = [
            'is_loan_available',
            'default_interest_rate',
            'min_interest_rate',
            'max_interest_rate',
            'min_loan_amount',
            'max_loan_amount',
            'max_loan_to_savings_ratio',
            'min_repayment_months',
            'max_repayment_months',
            'default_repayment_months',
            'processing_fee_percentage',
            'late_payment_penalty',
            'grace_period_days',
            'auto_approve_amount',
            'require_guarantors',
            'guarantors_required',
            'email_notifications',
            'sms_notifications',
            'payment_reminder_days',
        ];

        $loanSettingsData = Arr::only($data, $loanKeys);
        if (!empty($loanSettingsData) && Schema::hasTable('loan_settings')) {
            $loanSettings = LoanSetting::first();
            if ($loanSettings) {
                $loanSettings->update($loanSettingsData);
            } else {
                LoanSetting::create($loanSettingsData);
            }
            Cache::forget('loan_settings:default:v1');
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
                  ->orWhere('details', 'like', '%' . $request->search . '%')
                  ->orWhere('action', 'like', '%' . $request->search . '%');
            });
        }
        
        if ($request->filled('action')) {
            $query->where('action', $request->action);
        }
        
        if ($request->filled('user')) {
            $query->where('user', 'like', '%' . $request->user . '%');
        }
        
        if ($request->filled('date_from')) {
            $from = $this->normalizeDateTime((string) $request->date_from);
            if ($from) {
                $query->where('created_at', '>=', $from);
            }
        }
        
        if ($request->filled('date_to')) {
            $to = $this->normalizeDateTime((string) $request->date_to);
            if ($to) {
                $query->where('created_at', '<=', $to);
            }
        }
        
        if ($request->filled('sort')) {
            $query->orderBy('created_at', $request->sort == 'oldest' ? 'asc' : 'desc');
        } else {
            $query->orderBy('created_at', 'desc');
        }
        
        $perPage = (int) $request->get('per_page', 20);
        if ($perPage < 1) {
            $perPage = 1;
        }
        if ($perPage > 500) {
            $perPage = 500;
        }

        $logs = $query->paginate($perPage)->appends($request->except('page'));
        $logsData = $logs->getCollection()
            ->map(fn ($log) => $this->formatAuditLog($log))
            ->values()
            ->all();

        if ($request->ajax()) {
            return response()->json([
                'logs' => $logsData,
                'pagination' => [
                    'total' => $logs->total(),
                    'per_page' => $logs->perPage(),
                    'current_page' => $logs->currentPage(),
                    'last_page' => $logs->lastPage(),
                    'from' => $logs->firstItem(),
                    'to' => $logs->lastItem(),
                ],
            ]);
        }
        
        return view('admin.system.audit-logs', compact('settings', 'logs', 'logsData'));
    }

    public function backups()
    {
        $backups = DB::table('backups')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('admin.system.backups', compact('backups'));
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
            ->with('member:id,user_id,contact,location')
            ->where(function ($q) use ($log) {
                $q->where('username', $log->user)
                    ->orWhere('email', $log->user);
            })
            ->latest('id')
            ->first(['id', 'username', 'email', 'role_id', 'profile_picture']);

        $payloadChanges = Arr::get($changes, 'payload', []);
        $queryChanges = Arr::get($changes, 'query', []);
        $changeItems = $this->buildChangeItems($payloadChanges, $queryChanges);

        $resolvedEmail = $user?->email
            ?? (is_array($payloadChanges) ? (string) ($payloadChanges['email'] ?? '') : '')
            ?? '';
        $resolvedPhone = $user?->phone
            ?? $user?->member?->contact
            ?? (is_array($payloadChanges) ? (string) ($payloadChanges['phone'] ?? ($payloadChanges['contact'] ?? '')) : '')
            ?? '';
        $resolvedLocation = $user?->location
            ?? $user?->member?->location
            ?? (is_array($payloadChanges) ? (string) ($payloadChanges['location'] ?? '') : '')
            ?? '';

        $detailsText = is_scalar($log->details) || $log->details === null
            ? (string) $log->details
            : json_encode($log->details, JSON_UNESCAPED_UNICODE);

        return [
            'id' => (string) $log->id,
            'timestamp' => optional($log->created_at)->format('Y-m-d H:i:s') ?? (string) $log->timestamp,
            'user' => (string) $log->user,
            'userRole' => $user?->role ? ucfirst((string) $user->role) : 'System User',
            'userEmail' => $resolvedEmail !== '' ? $resolvedEmail : 'N/A',
            'userPhone' => $resolvedPhone !== '' ? $resolvedPhone : 'N/A',
            'userPhoto' => $user?->profile_picture_url ?? ('https://ui-avatars.com/api/?name=' . urlencode((string) $log->user) . '&background=3b82f6&color=fff'),
            'action' => ucfirst((string) $log->action),
            'module' => $this->resolveModule($changes),
            'details' => $detailsText,
            'description' => $this->buildDescription($detailsText, $payloadChanges, $queryChanges, $statusCode),
            'ip' => (string) ($changes['ip'] ?? 'N/A'),
            'location' => $resolvedLocation !== '' ? $resolvedLocation : 'N/A',
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
        $parts = [$details];

        if (is_array($payload) && $payload !== []) {
            $parts[] = 'Submitted data: ' . $this->summarizeKeyValues($payload);
        }

        if (is_array($query) && $query !== []) {
            $parts[] = 'Applied filters/params: ' . $this->summarizeKeyValues($query);
        }

        if ($statusCode > 0) {
            $parts[] = 'Result: HTTP ' . $statusCode . ' (' . $this->resolveStatusMeta($statusCode)[0] . ')';
        }

        return implode(' | ', array_filter($parts));
    }

    private function summarizeKeyValues(array $data): string
    {
        $items = [];
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $displayValue = json_encode($value, JSON_UNESCAPED_UNICODE);
            } elseif (is_bool($value)) {
                $displayValue = $value ? 'true' : 'false';
            } elseif ($value === null || $value === '') {
                $displayValue = 'null';
            } else {
                $displayValue = (string) $value;
            }

            if (mb_strlen($displayValue) > 60) {
                $displayValue = mb_substr($displayValue, 0, 57) . '...';
            }

            $items[] = $key . '=' . $displayValue;
        }

        if ($items === []) {
            return 'none';
        }

        return implode(', ', array_slice($items, 0, 8));
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

    private function normalizeDateTime(string $value): ?Carbon
    {
        $trimmed = trim($value);
        if ($trimmed === '') {
            return null;
        }

        try {
            return Carbon::parse($trimmed);
        } catch (\Throwable $e) {
            return null;
        }
    }
}
