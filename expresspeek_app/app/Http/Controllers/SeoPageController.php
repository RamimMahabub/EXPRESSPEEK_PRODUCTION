<?php

namespace App\Http\Controllers;

class SeoPageController extends Controller
{
    /**
     * Terms of Service page.
     */
    public function terms()
    {
        return view('pages.terms');
    }

    /**
     * Privacy Policy page.
     */
    public function privacy()
    {
        return view('pages.privacy');
    }

    /**
     * Help Center / FAQ page.
     */
    public function help()
    {
        return view('pages.help');
    }

    /**
     * Customer Service / Contact page.
     */
    public function customerService()
    {
        return view('pages.customer-service');
    }
}
