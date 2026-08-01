<?php

namespace App\Providers;

use App\Http\Responses\LoginResponse;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
use Laravel\Breeze\Contracts\LoginResponse as LoginResponseContract;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Bind custom login response for role-based redirect
        $this->app->singleton(LoginResponseContract::class, LoginResponse::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        $this->disableStaleViteHotFile();
    }

    private function disableStaleViteHotFile(): void
    {
        if (!$this->app->isLocal()) {
            return;
        }

        $hotFile = public_path('hot');

        if (!is_file($hotFile)) {
            return;
        }

        $hotUrl = trim((string) @file_get_contents($hotFile));

        if ($hotUrl === '' || !$this->isHotServerReachable($hotUrl)) {
            @unlink($hotFile);
        }
    }

    private function isHotServerReachable(string $hotUrl): bool
    {
        $parts = parse_url($hotUrl);

        if (!is_array($parts) || empty($parts['host'])) {
            return false;
        }

        $scheme = $parts['scheme'] ?? 'http';

        if (!in_array($scheme, ['http', 'https'], true)) {
            return false;
        }

        $host = $parts['host'];
        $port = $parts['port'] ?? ($scheme === 'https' ? 443 : 80);

        $errno = 0;
        $errstr = '';
        $connection = @fsockopen($host, (int) $port, $errno, $errstr, 0.35);

        if ($connection === false) {
            return false;
        }

        fclose($connection);

        $viteClientUrl = rtrim($hotUrl, '/') . '/@vite/client';

        $context = stream_context_create([
            'http' => [
                'timeout' => 0.6,
                'ignore_errors' => true,
            ],
            'https' => [
                'timeout' => 0.6,
                'ignore_errors' => true,
            ],
        ]);

        $response = @file_get_contents($viteClientUrl, false, $context);

        if ($response === false || $response === '') {
            return false;
        }

        return str_contains(strtolower($response), 'vite');
    }
}
