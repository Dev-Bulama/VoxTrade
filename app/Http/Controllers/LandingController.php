<?php

namespace App\Http\Controllers;

use App\Models\Setting;

class LandingController extends Controller
{
    public function index()
    {
        // Load all settings and pass to view
        $settings = Setting::pluck('value', 'key');
        return view('landing.index', compact('settings'));
    }

    public function terms()
    {
        $settings = Setting::pluck('value', 'key');
        return view('landing.terms', compact('settings'));
    }

    public function pricing()
    {
        $settings = Setting::pluck('value', 'key');
        return view('landing.pricing', compact('settings'));
    }
}
