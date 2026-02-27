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
        $activeRole = strtolower((string) $request->session()->get('active_role', $user->role));
        $userRole = strtolower((string) $user->role);

        // Prefer the active role in session when it's valid for the user.
        if ($activeRole !== '' && in_array($activeRole, $requiredRoles, true) && $user->hasRole($activeRole)) {
            if ($userRole !== $activeRole) {
                $user->forceFill(['role' => $activeRole])->save();
            }
            return $next($request);
        }

        if (!in_array($userRole, $requiredRoles, true)) {
            \Log::warning('Access denied', [
                'user' => $user->email,
                'user_role' => $userRole,
                'active_role' => $activeRole,
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
