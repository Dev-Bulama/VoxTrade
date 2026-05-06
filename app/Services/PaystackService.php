<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\Setting;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Http;

class PaystackService
{
    private string $secretKey;
    private string $baseUrl = 'https://api.paystack.co';

    public function __construct()
    {
        $this->secretKey = (string) config('services.paystack.secret_key', '');
    }

    public function initializeTransaction(User $user, float $amount, string $reference, array $metadata = []): ?array
    {
        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email'     => $user->email,
                'amount'    => (int) $amount,
                'reference' => $reference,
                'metadata'  => $metadata,
            ]);

        if (!$response->successful() || !$response->json('status')) {
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
