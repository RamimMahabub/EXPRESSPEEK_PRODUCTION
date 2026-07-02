<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{
    /**
     * Redirect the user to the Google authentication page.
     * We accept a 'role' parameter to know if they want to sign up as customer or property_owner.
     */
    public function redirect(Request $request, $role = 'customer')
    {
        // Default to customer if invalid role is passed
        if (!in_array($role, ['customer', 'property_owner'])) {
            $role = 'customer';
        }

        // Store role in session instead of overriding state parameter
        session(['oauth_role' => $role]);

        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from Google.
     */
    public function callback(Request $request)
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            // Retrieve the role from the session
            $role = session()->pull('oauth_role', 'customer');
            if (!in_array($role, ['customer', 'property_owner'])) {
                $role = 'customer';
            }

            // Find existing user by google_id or email
            $user = User::where('google_id', $googleUser->id)->orWhere('email', $googleUser->email)->first();

            if ($user) {
                // If the user exists but doesn't have the google_id set (e.g. they registered via email first)
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->id,
                        'avatar'    => $googleUser->avatar,
                    ]);
                }
            } else {
                // Create a new user
                $user = User::create([
                    'name'              => $googleUser->name,
                    'email'             => $googleUser->email,
                    'google_id'         => $googleUser->id,
                    'avatar'            => $googleUser->avatar,
                    // Use a random password since they logged in with Google
                    'password'          => bcrypt(Str::random(24)),
                    'role'              => $role,
                    'email_verified_at' => now(), // Google emails are already verified
                ]);
            }

            // Log the user in and regenerate session
            Auth::login($user, true);
            $request->session()->regenerate();

            // Redirect to their respective dashboard
            return redirect()->intended(route($user->getDashboardRoute()));

        } catch (\Exception $e) {
            // Handle error (e.g., user denied access, etc.)
            return redirect()->route('login')->with('error', 'Google login failed: ' . $e->getMessage());
        }
    }
}
