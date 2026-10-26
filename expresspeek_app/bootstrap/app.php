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
        $middleware->redirectUsersTo('/dashboard');

        // Trust Vercel proxy headers so request scheme/host are detected correctly.
        $middleware->trustProxies(at: '*');
        $middleware->validateCsrfTokens(except: [
            '/quote',
            'logout',
        ]);

        $middleware->alias([
            'role'        => \App\Http\Middleware\RoleMiddleware::class,
            'permission'  => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->respond(function ($response, $exception, Request $request) {
            if ($response->getStatusCode() === 419 && !$request->expectsJson()) {
                $next = $request->isMethod('get') ? url()->current() : url()->previous();
                return redirect()->route('login', ['next' => $next])->with(
                    'status',
                    'Your session expired due to inactivity. Please sign in again.'
                );
            }

            return $response;
        });
    })->create();
