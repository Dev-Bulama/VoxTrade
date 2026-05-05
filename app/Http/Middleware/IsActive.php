<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IsActive
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check() || auth()->user()->status !== 'active') {
            auth()->logout();

            return redirect()->route('login')
                ->with('error', 'Your account has been deactivated. Contact support.');
        }

        return $next($request);
    }
}
