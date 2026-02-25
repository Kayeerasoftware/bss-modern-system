<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        // Check if user has permission
        if (!$this->hasPermission($request->user(), $permission)) {
            abort(403, 'You do not have permission to access this resource');
        }

        return $next($request);
    }

    private function hasPermission($user, string $permission): bool
    {
        // Admin has all permissions
        if ($user->role === 'admin') {
            return true;
        }

        // Check role-based permissions
        $rolePermissions = config('permissions.' . $user->role, []);
        return in_array($permission, $rolePermissions);
    }
}
