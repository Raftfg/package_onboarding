<?php

namespace Raftfg\OnboardingPackage\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\RateLimiter;

class RateLimitOnboarding
{
    public function handle($request, Closure $next, $type = 'start')
    {
        $key = 'onboarding:' . $type . ':' . $request->ip();
        $limits = config("onboarding.rate_limits.{$type}", ['max_attempts' => 10, 'decay_minutes' => 60]);

        if (RateLimiter::tooManyAttempts($key, $limits['max_attempts'])) {
            return response()->json(['message' => 'Too many requests'], 429);
        }

        RateLimiter::hit($key, $limits['decay_minutes'] * 60);
        return $next($request);
    }
}
