<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

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
        
        return view('admin.system.audit-logs', compact('settings', 'logs'));
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
}
