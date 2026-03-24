<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle($request, Closure $next)
    {
        $user = auth('api')->user();

        if ($user && $user->role === 'admin') {
            return $next($request);
        }

        return response()->json([
            'message' => 'Access denied'
        ], 403);
    }
}