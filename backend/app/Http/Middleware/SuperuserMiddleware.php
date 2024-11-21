<?php


namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SuperuserMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->user() && $request->user()->role == 'superuser') {
            return $next($request);
        }

        return response()->json(['error' => 'No autorizado'], 403);
    }
}

