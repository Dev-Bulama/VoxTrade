<?php

namespace App\Services;

use App\Models\Payment;
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

    /**
     * Initialize a Paystack transaction.
     * Returns an array with authorization_url and reference, or null on failure.
     */
    public function initializeTransaction(User $user, float $amount, string $reference, array $metadata = []): ?array
    {
        $response = Http::withToken($this->secretKey)
            ->post("{$this->baseUrl}/transaction/initialize", [
                'email'     => $user->email,
                'amount'    => (int) $amount, // amount in kobo
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

    /**
     * Verify a Paystack transaction by its reference.
     * Returns transaction data array or null on failure.
     */
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
     * Get the plan price in kobo (NGN).
     * daily  = 500 NGN  = 50,000 kobo
     * weekly = 2,000 NGN = 200,000 kobo
     * monthly = 5,000 NGN = 500,000 kobo
     */
    public function getPlanPrice(string $plan): int
    {
        return match (strtolower($plan)) {
            'daily'   => 50000,
            'weekly'  => 200000,
            'monthly' => 500000,
            default   => 0,
        };
    }
}
