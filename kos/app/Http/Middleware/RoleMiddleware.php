<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        $user = $request->user();
        if (!$user) {
            abort(403, 'Unauthorized action.');
        }
        // Super Admin memiliki akses ke semua role routes
        if ($user->role === 'super_admin') {
            return $next($request);
        }
        if ($user->role !== $role) {
            abort(403, 'Unauthorized action.');
        }

        return $next($request);
    }
}
