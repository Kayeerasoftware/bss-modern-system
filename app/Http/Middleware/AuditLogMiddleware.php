<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Models\System\AuditLog;
use Illuminate\Support\Facades\Log;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $response = $next($request);
        
        if (auth()->check() && in_array($request->method(), ['POST', 'PUT', 'PATCH', 'DELETE'])) {
            try {
                $log = AuditLog::create([
                    'user' => auth()->user()->name ?? 'Unknown',
                    'action' => $this->getAction($request),
                    'details' => $this->getDetails($request),
                    'timestamp' => now(),
                    'changes' => $request->except(['_token', '_method', 'password', 'password_confirmation']),
                ]);
                Log::info('Audit log created: ' . $log->id . ' with changes: ' . json_encode($request->except(['_token', '_method', 'password', 'password_confirmation'])));
            } catch (\Exception $e) {
                Log::error('Audit log failed: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            }
        }

        return $response;
    }

    private function getAction($request)
    {
        $method = $request->method();
        $route = $request->route() ? $request->route()->getName() : '';

        if (str_contains($route, 'store')) return 'create';
        if (str_contains($route, 'update')) return 'update';
        if (str_contains($route, 'destroy') || str_contains($route, 'delete')) return 'delete';
        if ($method === 'POST') return 'create';
        if ($method === 'PUT' || $method === 'PATCH') return 'update';
        if ($method === 'DELETE') return 'delete';
        
        return 'action';
    }

    private function getDetails($request)
    {
        $path = $request->path();
        $parts = explode('/', $path);
        $role = $parts[0] ?? 'system';
        $module = $parts[1] ?? 'unknown';
        $action = $this->getAction($request);
        $route = $request->route() ? $request->route()->getName() : 'unknown';
        
        // Get more context
        $user = auth()->user();
        $description = ucfirst($action) . ' operation in ' . ucfirst($module) . ' module by ' . $user->name . ' (' . ucfirst($user->role ?? 'user') . ')';
        
        // Add specific details based on route
        if ($request->has('id')) {
            $description .= ' - ID: ' . $request->input('id');
        }
        if ($request->route() && $request->route()->parameter('id')) {
            $description .= ' - Record ID: ' . $request->route()->parameter('id');
        }
        
        return $description;
    }
}
