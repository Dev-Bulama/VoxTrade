<?php

namespace App\Services;

use App\Models\ApiKey;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PaystackService
{
    private string $secretKey;
    private string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        // Prefer key saved via Admin → API Keys panel (encrypted in DB),
        // fall back to PAYSTACK_SECRET_KEY in .env
        $dbKey = ApiKey::getApiKey('paystack');
        $this->secretKey = (string) ($dbKey ?: config('services.paystack.secret_key', ''));
    }

    public function initializeTransaction(User $user, float $amount, string $reference, array $metadata = []): ?array
    {
        if (empty($this->secretKey)) {
            Log::error('PaystackService: secret key is not configured. Add it via Admin → API Keys (service: paystack) or set PAYSTACK_SECRET_KEY in .env');
            return null;
        }

        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email'     => $user->email,
                'amount'    => (int) $amount,
                'reference' => $reference,
                'metadata'  => $metadata,
            ]);

        if (!$response->successful() || !$response->json('status')) {
            Log::warning('PaystackService: initializeTransaction failed', [
                'status' => $response->status(),
                'body'   => $response->body(),
            ]);
            return null;
        }

        $data = $response->json('data');

        return [
            'authorization_url' => $data['authorization_url'] ?? null,
            'reference'         => $data['reference'] ?? $reference,
            'access_code'       => $data['access_code'] ?? null,
        ];
    }

    public function verifyTransaction(string $reference): ?array
    {
        $response = Http::withToken($this->secretKey)
            ->get("{$this->baseUrl}/transaction/verify/{$reference}");

        if (!$response->successful() || !$response->json('status')) {
            return null;
        }

        return $response->json('data');
    }

    /**
     * Get plan price in kobo (NGN). Reads from DB settings, falls back to defaults.
     * Admin can override via Settings page.
     */
    public function getPlanPrice(string $plan): int
    {
        $defaults = ['daily' => 500, 'weekly' => 2000, 'monthly' => 5000];
        $plan     = strtolower($plan);
        if (!isset($defaults[$plan])) return 0;

        $ngnPrice = (int) Setting::get("price_{$plan}", $defaults[$plan]);
        return $ngnPrice * 100; // convert NGN to kobo
    }

    /**
     * Get all plan prices in NGN (for display).
     */
    public static function planPricesNGN(): array
    {
        return [
            'daily'   => (int) Setting::get('price_daily',   500),
            'weekly'  => (int) Setting::get('price_weekly',  2000),
            'monthly' => (int) Setting::get('price_monthly', 5000),
        ];
    }
}
