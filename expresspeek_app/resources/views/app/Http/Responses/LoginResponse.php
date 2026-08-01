<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Laravel\Breeze\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = auth()->user();

        if ($user->isAdmin()) {
            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->isAgent()) {
            return redirect()->intended(route('agent.dashboard'));
        }

        // Customers land on their own dashboard
        return redirect()->intended(route('customer.dashboard'));
    }
}
