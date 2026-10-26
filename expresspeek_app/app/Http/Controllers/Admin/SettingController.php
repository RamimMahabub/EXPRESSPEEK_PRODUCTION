<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function updatePaymentSettings(Request $request)
    {
        $data = $request->validate([
            'payment_bkash_number' => ['nullable', 'string', 'max:255'],
            'payment_bank_name' => ['nullable', 'string', 'max:255'],
            'payment_bank_account_name' => ['nullable', 'string', 'max:255'],
            'payment_bank_account_no' => ['nullable', 'string', 'max:255'],
            'payment_bank_branch' => ['nullable', 'string', 'max:255'],
        ]);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Payment settings updated successfully.');
    }
}
