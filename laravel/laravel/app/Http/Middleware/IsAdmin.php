<?php

namespace App\Http\Middleware;

use Closure;

class IsAdmin
{
    public function handle($request, Closure $next)
    {
        abort_unless(auth()->check() && auth()->user()->isAdmin(), 403);

        return $next($request);
    }
}
