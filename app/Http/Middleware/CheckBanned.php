<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckBanned
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check() && auth()->user()->is_banned) {
            // Allow logout
            if ($request->routeIs('logout')) {
                return $next($request);
            }

            return response()->view('banned', [
                'reason' => auth()->user()->ban_reason,
                'bannedAt' => auth()->user()->banned_at,
            ], 403);
        }

        return $next($request);
    }
}
