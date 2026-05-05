<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsSubscribed
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || !auth()->user()->hasActiveSubscription()) {
            return redirect()->route('subscription.plans')
                ->with('warning', 'Your subscription has expired. Please renew to access signals.');
        }

        return $next($request);
    }
}
