<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ApiKey;
use Illuminate\Http\Request;

class ApiKeyController extends Controller
{
    public function index()
    {
        $apiKeys  = ApiKey::all();
        $services = ['openai', 'binance', 'alpha_vantage', 'tradingview', 'paystack', 'flutterwave', 'telegram'];
        return view('admin.api-keys.index', compact('apiKeys', 'services'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'service_name' => 'required|string|max:50',
            'api_key'      => 'required|string',
            'api_secret'   => 'nullable|string',
            'extra_config' => 'nullable|string',
        ]);

        $extraConfig = null;
        if (!empty($data['extra_config'])) {
            json_decode($data['extra_config']);
            if (json_last_error() === JSON_ERROR_NONE) {
                $extraConfig = json_decode($data['extra_config'], true);
            }
        }

        ApiKey::updateOrCreate(
            ['service_name' => $data['service_name']],
            [
                'api_key'      => $data['api_key'],
                'api_secret'   => $data['api_secret'] ?? null,
                'extra_config' => $extraConfig,
            ]
        );

        return back()->with('success', ucfirst($data['service_name']) . ' API key saved.');
    }

    public function update(Request $request, ApiKey $apiKey)
    {
        $data = $request->validate([
            'api_key'    => 'required|string',
            'api_secret' => 'nullable|string',
        ]);
        $apiKey->update($data);
        return back()->with('success', 'API key updated.');
    }

    public function destroy(ApiKey $apiKey)
    {
        $apiKey->delete();
        return back()->with('success', 'API key removed.');
    }

    public function toggle(ApiKey $apiKey)
    {
        $apiKey->update(['is_active' => !$apiKey->is_active]);
        return back()->with('success', 'API key ' . ($apiKey->is_active ? 'enabled' : 'disabled') . '.');
    }
}
