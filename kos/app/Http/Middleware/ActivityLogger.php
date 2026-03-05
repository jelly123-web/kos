<?php

namespace App\Http\Middleware;

use App\Models\ActivityLog;
use App\Support\PermissionRegistry;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ActivityLogger
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Skip health check, assets, and geo tracking endpoint
        $routeName = optional($request->route())->getName();
        if (!$routeName || str_starts_with($routeName, 'ignition') || $routeName === 'track.geo') {
            return $response;
        }

        // Only log when authenticated
        $user = $request->user();
        if (!$user) {
            return $response;
        }

        // Map route → permission label as 'action'
        $map = PermissionRegistry::routeMap();
        $permKey = $map[$routeName] ?? null;
        $labels = PermissionRegistry::all();
        $action = $labels[$permKey] ?? strtoupper($request->method()).' '.$request->path();

        $lat = session('geo.lat'); $lng = session('geo.lng');

        try {
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => $action,
                'route_name' => $routeName,
                'method' => $request->method(),
                'url' => $request->fullUrl(),
                'ip' => $request->ip(),
                'user_agent' => substr((string)$request->userAgent(), 0, 1024),
                'lat' => $lat,
                'lng' => $lng,
            ]);
        } catch (\Throwable $e) {
            // swallow
        }

        return $response;
    }
}
