<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the customer login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Display the internal team login view.
     */
    public function createAdmin(): View
    {
        return view('auth.admin-login');
    }

    /**
     * Handle an incoming customer authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $user = $request->user();

        if ($user?->isInternalUser()) {
            Auth::guard('web')->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return back()->withErrors([
                'login' => 'Internal users must login from /admin/login.',
            ])->onlyInput('login');
        }

        if (! $user?->hasVerifiedEmail()) {
            return redirect()->route('verification.notice');
        }

        return redirect()->intended('/');
    }

    /**
     * Handle an incoming internal user password check and send OTP.
     */
    public function storeAdmin(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user || ! Hash::check($credentials['password'], (string) $user->password)) {
            return back()->withErrors([
                'email' => trans('auth.failed'),
            ])->onlyInput('email');
        }

        if (! $user->isInternalUser()) {
            return back()->withErrors([
                'email' => 'This login is only for admin and staff users.',
            ])->onlyInput('email');
        }

        // Bypass OTP for easy testing
        Auth::login($user, $request->boolean('remember'));
        $request->session()->regenerate();
        
        return redirect()->intended(route('admin.dashboard', absolute: false));
    }

    /**
     * Show OTP input form for pending internal login.
     */
    public function createAdminOtp(Request $request): View|RedirectResponse
    {
        if (! $request->session()->has('pending_admin_login_user_id')) {
            return redirect()->route('admin.login')->with('status', 'Please login first.');
        }

        return view('auth.admin-otp');
    }

    /**
     * Verify OTP and complete internal sign-in.
     */
    public function verifyAdminOtp(Request $request): RedirectResponse
    {
        $request->validate([
            'otp' => ['required', 'digits:6'],
        ]);

        $pendingUserId = (int) $request->session()->get('pending_admin_login_user_id');
        $otpHash = Cache::get($this->otpCacheKey($pendingUserId));

        if (! $pendingUserId || ! is_string($otpHash) || ! Hash::check((string) $request->otp, $otpHash)) {
            return back()->withErrors([
                'otp' => 'Invalid or expired OTP. Please login again.',
            ]);
        }

        Auth::loginUsingId($pendingUserId, (bool) $request->session()->pull('pending_admin_login_remember', false));

        Cache::forget($this->otpCacheKey($pendingUserId));
        $request->session()->forget('pending_admin_login_user_id');
        $request->session()->regenerate();

        return redirect()->intended(route('admin.dashboard', absolute: false));
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

    private function otpCacheKey(int $userId): string
    {
        return 'admin_login_otp_'.$userId;
    }
}
