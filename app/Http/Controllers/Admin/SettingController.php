<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::whereIn('key', ['refresh_interval', 'default_risk_level', 'ai_sensitivity'])->pluck('value', 'key');
        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'refresh_interval'  => 'required|integer|min:1|max:60',
            'default_risk_level' => 'required|in:low,medium,high',
            'ai_sensitivity'    => 'required|integer|min:1|max:100',
        ]);
        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }
        return back()->with('success', 'System settings updated.');
    }
}
