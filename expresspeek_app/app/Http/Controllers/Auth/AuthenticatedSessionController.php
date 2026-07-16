<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return response(view('auth.login'))
            ->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Never reuse an intended URL saved by the previous account. It may point
        // to a dashboard that the newly authenticated role cannot access.
        $request->session()->forget('url.intended');

        $next = $this->resolveRedirectTarget(
            $request,
            (string) $request->query('next', ''),
            auth()->user()
        );
        if ($next !== null) {
            return redirect()->to($next);
        }

        return redirect()->to($this->postLoginRedirectFor(auth()->user()));
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

    private function resolveRedirectTarget(Request $request, string $next, ?\App\Models\User $user): ?string
    {
        $next = trim($next);

        if ($next === '') {
            return null;
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
        if ($host !== '' && $host !== strtolower($request->getHost())) {
            return null;
        }

        $target = $parts['path'];

        if (!empty($parts['query'])) {
            $target .= '?' . $parts['query'];
        }

        if (!empty($parts['fragment'])) {
            $target .= '#' . $parts['fragment'];
        }

        if (($user?->isAdmin() && preg_match('#^/(agent|customer)(/|$)#', $target))
            || ($user?->isAgent() && preg_match('#^/(admin|customer)(/|$)#', $target))
            || ($user?->isCustomer() && preg_match('#^/(admin|agent)(/|$)#', $target))) {
            return null;
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
