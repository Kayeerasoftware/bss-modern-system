<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use App\Services\AuditLogService;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AuditLogMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        /** @var Response $response */
        $response = $next($request);

        $actor = $this->resolveActor($request);

        if (!$actor || !$this->shouldLog($request, $response)) {
            return $response;
        }

        $action = $this->resolveAction($request, $response);
        $details = $this->buildDetails($request, $action, $response);
        $payload = $request->except(['_token', '_method', 'password', 'password_confirmation', 'current_password']);

        AuditLogService::log($actor, $action, $details, [
            'method' => $request->method(),
            'route' => $request->route()?->getName(),
            'path' => $request->path(),
            'url' => $request->fullUrl(),
            'status' => $response->getStatusCode(),
            'ip' => $request->ip(),
            'user_agent' => (string) $request->userAgent(),
            'query' => $request->query(),
            'payload' => $payload,
            'changed_fields' => array_keys(Arr::except($payload, ['remember'])),
        ]);

        return $response;
    }

    private function resolveActor(Request $request)
    {
        $actor = $request->user();
        if ($actor) {
            return $actor;
        }

        foreach (['sanctum', 'web', 'member'] as $guard) {
            if (!config("auth.guards.{$guard}")) {
                continue;
            }

            try {
                $guardActor = Auth::guard($guard)->user();
                if ($guardActor) {
                    return $guardActor;
                }
            } catch (Throwable) {
                // Never break page rendering due to an unavailable auth guard.
                continue;
            }
        }

        return null;
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

        if (!config('audit.log_get_requests', true)) {
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

        // Skip static resources and framework internals.
        if (str_starts_with($path, '_debugbar')
            || str_starts_with($path, 'storage/')
            || str_starts_with($path, 'build/')
            || str_starts_with($path, 'vendor/')) {
            return false;
        }

        return true;
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

    private function buildDetails(Request $request, string $action, Response $response): string
    {
        $segments = explode('/', trim($request->path(), '/'));
        $area = $segments[0] ?? 'system';
        $module = $segments[1] ?? ($segments[0] ?? 'unknown');
        $user = $this->resolveActor($request);
        $route = (string) ($request->route()?->getName() ?? $request->path());
        $status = $response->getStatusCode();

        $details = ucfirst($action).' request on '.strtoupper((string) $area).' / '.ucfirst((string) $module)
            .' by '.($user->name ?? 'Unknown').' ('.($user->role ?? 'unknown').')'
            .' via '.$route.' [HTTP '.$status.']';

        $id = $request->route()?->parameter('id') ?? $request->route()?->parameter('memberId');
        if ($id !== null) {
            $details .= ' - Record ID: '.$id;
        }

        $fields = array_keys($request->except(['_token', '_method', 'password', 'password_confirmation', 'current_password']));
        if ($fields !== []) {
            $details .= ' - Fields: '.implode(', ', array_slice($fields, 0, 10));
        }

        return $details;
    }
}
