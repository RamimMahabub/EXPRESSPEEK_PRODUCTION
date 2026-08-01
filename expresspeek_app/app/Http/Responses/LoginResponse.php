<?php

namespace App\Http\Responses;

use Illuminate\Http\RedirectResponse;
use Laravel\Breeze\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): RedirectResponse
    {
        $request->session()->forget('url.intended');

        return redirect()->route($request->user()->dashboardRouteName());
    }
}
