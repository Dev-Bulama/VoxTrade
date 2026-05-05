<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class CmsController extends Controller
{
    public function index()
    {
        $cms = Setting::whereIn('key', [
            'site_name', 'hero_title', 'hero_subtitle', 'features_title',
            'pricing_title', 'footer_text', 'terms_content', 'disclaimer',
        ])->pluck('value', 'key');
        return view('admin.cms', compact('cms'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'site_name'      => 'required|string|max:100',
            'hero_title'     => 'required|string|max:200',
            'hero_subtitle'  => 'required|string|max:500',
            'features_title' => 'required|string|max:200',
            'pricing_title'  => 'required|string|max:200',
            'footer_text'    => 'required|string|max:500',
            'terms_content'  => 'required|string',
            'disclaimer'     => 'required|string|max:500',
        ]);
        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }
        return back()->with('success', 'CMS content updated successfully.');
    }
}
