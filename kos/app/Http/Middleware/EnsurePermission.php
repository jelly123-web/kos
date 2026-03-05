<?php

namespace App\Http\Middleware;

use App\Models\RolePermission;
use App\Support\PermissionRegistry;
use Closure;
use Illuminate\Http\Request;

class EnsurePermission
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        if (!$user) return $next($request);

        $routeName = optional($request->route())->getName();
        if (!$routeName) return $next($request);

        $map = PermissionRegistry::routeMap();
        if (!isset($map[$routeName])) {
            return $next($request);
        }
        $permKey = $map[$routeName];
        if (!RolePermission::allows($user->role, $permKey)) {
            abort(403, 'Akses tidak diizinkan untuk fitur ini.');
        }
        return $next($request);
    }
}
