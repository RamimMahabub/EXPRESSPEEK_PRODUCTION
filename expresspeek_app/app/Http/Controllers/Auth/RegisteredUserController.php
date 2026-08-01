<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'phone_country_code' => ['required', 'string', 'regex:/^\+[1-9]\d{0,3}$/'],
            'phone' => ['required', 'string', 'regex:/^[0-9\s().-]{6,20}$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'terms' => ['accepted'],
        ]);

        $normalizedPhone = User::normalizePhone(
            (string) $request->phone_country_code,
            (string) $request->phone,
        );

        if (! preg_match('/^\+[1-9]\d{7,14}$/', $normalizedPhone)) {
            throw ValidationException::withMessages([
                'phone' => 'Enter a valid mobile number including the correct country code.',
            ]);
        }

        if (User::query()->where('phone', $normalizedPhone)->exists()) {
            throw ValidationException::withMessages([
                'phone' => 'This phone number is already connected to an account.',
            ]);
        }

        $user = DB::transaction(function () use ($request, $normalizedPhone): User {
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'phone_country_code' => $request->phone_country_code,
                'phone' => $normalizedPhone,
                'password' => Hash::make($request->password),
            ]);

            $user->assignRole('customer');

            return $user;
        });

        event(new Registered($user));

        Auth::login($user);

        $next = (string) $request->query('next', '');
        if ($next !== '' && str_starts_with($next, '/')) {
            return redirect()->to($next);
        }

        return redirect(route('dashboard', absolute: false));
    }
}
