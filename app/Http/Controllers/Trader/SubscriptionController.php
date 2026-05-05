<?php

namespace App\Http\Controllers\Trader;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Subscription;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SubscriptionController extends Controller
{
    public function __construct(private PaystackService $paystack) {}

    public function plans()
    {
        $user = auth()->user();
        $subscription = $user->subscription;
        $plans = [
            'daily'   => ['name' => 'Daily',   'price' => 500,  'features' => ['All live signals', 'AI analysis', '24hr access']],
            'weekly'  => ['name' => 'Weekly',  'price' => 2000, 'features' => ['All live signals', 'AI analysis', '7-day access', 'Performance analytics']],
            'monthly' => ['name' => 'Monthly', 'price' => 5000, 'features' => ['All live signals', 'AI analysis', '30-day access', 'Performance analytics', 'Priority support']],
        ];
        return view('trader.subscription', compact('plans', 'subscription'));
    }

    public function initialize(Request $request)
    {
        $request->validate(['plan' => 'required|in:daily,weekly,monthly']);
        $user = auth()->user();
        $plan = $request->plan;
        $amount = $this->paystack->getPlanPrice($plan) / 100;
        $reference = 'VTX-' . strtoupper(Str::random(12));

        $payment = Payment::create([
            'user_id'   => $user->id,
            'reference' => $reference,
            'amount'    => $amount,
            'currency'  => 'NGN',
            'gateway'   => 'paystack',
            'status'    => 'pending',
        ]);

        $result = $this->paystack->initializeTransaction($user, $amount * 100, $reference, ['plan' => $plan]);

        if (!$result) {
            return back()->with('error', 'Could not initialize payment. Please try again.');
        }

        // Store plan in session for callback
        session(['pending_plan' => $plan, 'pending_reference' => $reference]);
        return redirect($result['authorization_url']);
    }

    public function callback(Request $request)
    {
        $reference = $request->reference ?? session('pending_reference');
        if (!$reference) return redirect()->route('subscription.plans')->with('error', 'Invalid payment reference.');

        $result  = $this->paystack->verifyTransaction($reference);
        $payment = Payment::where('reference', $reference)->first();

        if ($result && $result['status'] === 'success') {
            $plan = session('pending_plan', 'monthly');
            $expiresAt = match($plan) {
                'daily'   => now()->addDay(),
                'weekly'  => now()->addWeek(),
                'monthly' => now()->addMonth(),
            };

            $subscription = Subscription::create([
                'user_id'    => auth()->id(),
                'plan'       => $plan,
                'amount'     => $payment->amount ?? 0,
                'currency'   => 'NGN',
                'expires_at' => $expiresAt,
                'status'     => 'active',
            ]);

            if ($payment) {
                $payment->update([
                    'status'           => 'successful',
                    'subscription_id'  => $subscription->id,
                    'gateway_response' => $result,
                ]);
            }

            session()->forget(['pending_plan', 'pending_reference']);
            return redirect()->route('dashboard')->with('success', 'Subscription activated! Welcome to VoxTrade.');
        }

        if ($payment) $payment->update(['status' => 'failed']);
        return redirect()->route('subscription.plans')->with('error', 'Payment verification failed. Please try again.');
    }

    public function webhook(Request $request)
    {
        // Verify Paystack webhook signature
        $signature         = $request->header('x-paystack-signature');
        $secretKey         = config('services.paystack.secret_key');
        $computedSignature = hash_hmac('sha512', $request->getContent(), $secretKey);

        if ($signature !== $computedSignature) {
            return response()->json(['status' => 'invalid'], 400);
        }

        $payload = $request->json()->all();
        if ($payload['event'] === 'charge.success') {
            $reference = $payload['data']['reference'];
            $payment   = Payment::where('reference', $reference)->first();
            if ($payment && $payment->status === 'pending') {
                $payment->update(['status' => 'successful', 'gateway_response' => $payload['data']]);
            }
        }

        return response()->json(['status' => 'ok']);
    }

    public function status()
    {
        $user = auth()->user();
        return view('trader.subscription-status', ['subscription' => $user->subscription]);
    }
}
