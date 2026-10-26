<?php

namespace App\Http\Controllers;

use App\Models\BusinessApplication;
use Illuminate\Http\Request;

class BusinessController extends Controller
{
    public function index()
    {
        return view('business.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proprietor_name' => 'required|string|max:255',
            'nid_no' => 'required|string|max:255',
            'company_name' => 'required|string|max:255',
            'trade_license' => 'nullable|string|max:255',
            'address' => 'required|string',
            'mobile_no' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        BusinessApplication::create($validated);

        return redirect()->back()->with('success', 'Your Business Application has been submitted successfully. Our team will contact you soon!');
    }
}
