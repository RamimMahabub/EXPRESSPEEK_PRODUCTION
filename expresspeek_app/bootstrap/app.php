<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust Vercel proxy headers so request scheme/host are detected correctly.
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            '/quote',
            'logout',
        ]);

        $middleware->alias([
            'role'        => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission'  => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // A login page left open before logout contains the previous session's
        // CSRF token. Recover with a fresh form instead of showing a raw 419 page.
        $exceptions->respond(function ($response, $exception, Request $request) {
            if ($response->getStatusCode() === 419
                && $request->isMethod('post')
                && $request->is('login')) {
                return redirect()->route('login', $request->only('next'))->with(
                    'status',
                    'Your login page expired. Please sign in again.'
                );
            }

            return $response;
        });
    })->create();
