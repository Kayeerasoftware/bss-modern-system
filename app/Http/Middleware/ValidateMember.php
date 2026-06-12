<?php

namespace App\Http\Middleware;

use Closure;

class ValidateMember
{
    public function handle($request, Closure $next)
    {
        return $next($request);
    }
}
