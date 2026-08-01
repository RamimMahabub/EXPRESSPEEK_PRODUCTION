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
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        $acceptedRoles = collect($roles)
            ->flatMap(fn (string $role) => explode('|', $role))
            ->filter()
            ->values()
            ->all();

        if ($request->user()->hasAnyRole($acceptedRoles)) {
            return $next($request);
        }

        if (in_array($request->method(), ['GET', 'HEAD'], true) && !$request->expectsJson()) {
            return redirect()
                ->route($request->user()->dashboardRouteName())
                ->with('error', 'That section is not available for your account role.');
        }

        abort(403, 'Unauthorized. You do not have the required role to access this page.');
    }
}
