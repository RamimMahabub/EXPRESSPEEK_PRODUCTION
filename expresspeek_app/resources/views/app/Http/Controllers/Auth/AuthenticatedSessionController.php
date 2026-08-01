<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $next = $this->resolveRedirectTarget($request, (string) $request->query('next', ''));
        if ($next !== null) {
            return redirect()->to($next);
        }

        return redirect()->intended($this->postLoginRedirectFor(auth()->user()));
    }

    private function postLoginRedirectFor(?\App\Models\User $user): string
    {
        if ($user?->isAdmin()) {
            return route('admin.dashboard', absolute: false);
        }

        if ($user?->isAgent()) {
            return route('agent.dashboard', absolute: false);
        }

        if ($user?->isCustomer()) {
            return route('customer.dashboard', absolute: false);
        }

        return route('dashboard', absolute: false);
    }

    private function resolveRedirectTarget(Request $request, string $next): ?string
    {
        $next = trim($next);

        if ($next === '') {
            return null;
        }

        if (str_starts_with($next, '/')) {
            return $next;
        }

        $parts = parse_url($next);

        if (!is_array($parts) || empty($parts['path'])) {
            return null;
        }

        $scheme = strtolower((string) ($parts['scheme'] ?? ''));
        if ($scheme !== '' && !in_array($scheme, ['http', 'https'], true)) {
            return null;
        }

        $host = strtolower((string) ($parts['host'] ?? ''));
        if ($host === '' || $host !== strtolower($request->getHost())) {
            return null;
        }

        $target = $parts['path'];

        if (!empty($parts['query'])) {
            $target .= '?' . $parts['query'];
        }

        if (!empty($parts['fragment'])) {
            $target .= '#' . $parts['fragment'];
        }

        return $target;
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
