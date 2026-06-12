<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();
        if (!$user) {
            return redirect()->route('login')->with('error', 'Please login to continue');
        }

        $requiredRoles = array_map(fn ($role) => strtolower((string) $role), $roles);
        $userRole = strtolower((string) $user->role);

        if (empty($requiredRoles)) {
            return $next($request);
        }

        $allowed = false;
        $matchedRole = null;
        foreach ($requiredRoles as $requiredRole) {
            if ($userRole === $requiredRole || $user->hasRole($requiredRole)) {
                $allowed = true;
                $matchedRole = $requiredRole;
                break;
            }
        }

        if ($allowed) {
            // Request-scoped role context. Avoid mutating shared session or DB role.
            $request->attributes->set('active_role', $matchedRole);
            return $next($request);
        }

        if (!in_array($userRole, $requiredRoles, true)) {
            \Log::warning('Access denied', [
                'user' => $user->email,
                'user_role' => $userRole,
                'required_roles' => $requiredRoles,
                'url' => $request->url()
            ]);

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => "Unauthorized access for role '{$userRole}'."
                ], 403);
            }

            return redirect()->route('dashboard')->with('error', 'You do not have permission for that page using your current role.');
        }

        return $next($request);
    }
}
