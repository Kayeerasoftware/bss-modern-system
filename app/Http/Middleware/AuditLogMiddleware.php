<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\AuditLogService;
use Symfony\Component\HttpFoundation\Response;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        if (!$request->user() || !$this->shouldLog($request, $response)) {
            return $response;
        }

        $action = $this->resolveAction($request, $response);
        $details = $this->buildDetails($request, $action);

        AuditLogService::log($request->user(), $action, $details, [
            'method' => $request->method(),
            'route' => $request->route()?->getName(),
            'path' => $request->path(),
            'url' => $request->fullUrl(),
            'status' => $response->getStatusCode(),
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'query' => $request->query(),
            'payload' => $request->except(['_token', '_method', 'password', 'password_confirmation']),
        ]);

        return $response;
    }

    private function shouldLog(Request $request, Response $response): bool
    {
        if ($response->getStatusCode() >= 500) {
            // Avoid logging noisy internal errors as user actions.
            return false;
        }

        $method = strtoupper($request->method());
        if (in_array($method, ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return true;
        }

        if ($method !== 'GET') {
            return false;
        }

        $routeName = (string) ($request->route()?->getName() ?? '');
        $path = strtolower($request->path());

        // Skip very noisy chat polling endpoints for GET requests.
        if (str_starts_with($routeName, 'chat.messages')
            || str_starts_with($routeName, 'chat.conversations')
            || $routeName === 'chat.unread-count') {
            return false;
        }

        if ($request->query()) {
            return true; // capture checks/search/filter operations
        }

        foreach (['download', 'export', 'report', 'audit', 'settings', 'dashboard'] as $keyword) {
            if (str_contains($routeName, $keyword) || str_contains($path, $keyword)) {
                return true;
            }
        }

        return false;
    }

    private function resolveAction(Request $request, Response $response): string
    {
        $method = strtoupper($request->method());
        $routeName = strtolower((string) ($request->route()?->getName() ?? ''));
        $path = strtolower($request->path());

        if ($routeName === 'switch.role') {
            return 'role_switch';
        }

        if ($method === 'DELETE' || str_contains($routeName, 'destroy') || str_contains($path, 'delete')) {
            return 'delete';
        }
        if (in_array($method, ['PUT', 'PATCH'], true) || str_contains($routeName, 'update')) {
            return 'update';
        }
        if ($method === 'POST' || str_contains($routeName, 'store') || str_contains($path, 'create')) {
            return 'create';
        }
        if (str_contains($routeName, 'download')
            || str_contains($routeName, 'export')
            || str_contains($path, 'download')
            || str_contains($path, 'export')
            || str_contains((string) $response->headers->get('content-disposition'), 'attachment')) {
            return 'download';
        }
        if ($request->query()) {
            return 'check';
        }

        return 'view';
    }

    private function buildDetails(Request $request, string $action): string
    {
        $segments = explode('/', trim($request->path(), '/'));
        $area = $segments[0] ?? 'system';
        $module = $segments[1] ?? ($segments[0] ?? 'unknown');
        $user = $request->user();

        $details = ucfirst($action).' in '.strtoupper((string) $area).' / '.ucfirst((string) $module)
            .' by '.($user->name ?? 'Unknown').' ('.($user->role ?? 'unknown').')';

        $id = $request->route()?->parameter('id') ?? $request->route()?->parameter('memberId');
        if ($id !== null) {
            $details .= ' - Record ID: '.$id;
        }

        return $details;
    }
}
