<?php

namespace Raftfg\OnboardingPackage\Http\Middleware;

use Closure;
use Raftfg\OnboardingPackage\Models\Application;
use Illuminate\Support\Facades\Hash;

class MasterKeyAuth
{
    public function handle($request, Closure $next)
    {
        $masterKey = $request->header('X-Master-Key');
        if (!$masterKey) {
            return response()->json(['message' => 'X-Master-Key missing'], 401);
        }

        $application = Application::where('status', 'active')->get()->first(function ($app) use ($masterKey) {
            return Hash::check($masterKey, $app->master_key);
        });

        if (!$application) {
            return response()->json(['message' => 'Invalid Master Key'], 401);
        }

        $request->attributes->set('application', $application);
        return $next($request);
    }
}
