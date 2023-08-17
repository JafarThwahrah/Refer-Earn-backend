<?php

namespace App\Http\Middleware;

use Closure;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        if (auth()->guard('api')->check() && auth('api')->user()->is_admin) {
            return $next($request);
        }

        return response()->json(['message' => 'Unauthorized'], 401);
    }
}
