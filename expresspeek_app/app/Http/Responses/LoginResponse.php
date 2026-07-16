<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Laravel\Breeze\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $user = auth()->user();

        $request->session()->forget('url.intended');

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isAgent()) {
            return redirect()->route('agent.dashboard');
        }

        // Customers land on their own dashboard
        return redirect()->route('customer.dashboard');
    }
}
