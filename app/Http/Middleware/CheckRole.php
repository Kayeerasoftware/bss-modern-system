<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckRole
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login')->with('error', 'Please login to continue');
        }

        $userRole = $request->user()->role;
        
        if (!in_array($userRole, $roles)) {
            \Log::warning('Access denied', [
                'user' => $request->user()->email,
                'user_role' => $userRole,
                'required_roles' => $roles,
                'url' => $request->url()
            ]);
            abort(403, "Unauthorized access. Your role ($userRole) does not have permission.");
        }

        return $next($request);
    }
}
