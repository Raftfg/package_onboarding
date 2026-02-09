<?php

namespace Raftfg\OnboardingPackage\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OnboardingController extends Controller
{
    public function welcome() { return view('onboarding::onboarding.welcome'); }
    public function showInitialForm() { return view('onboarding::onboarding.initial-form'); }

    public function storeInitialData(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'organization_name' => 'nullable|string',
        ]);

        $token = Str::random(32);
        Cache::put("onboarding_data_{$token}", $validated, now()->addMinutes(10));
        
        return redirect()->route('onboarding.loading', ['token' => $token]);
    }

    public function showLoading(Request $request) { return view('onboarding::onboarding.loading'); }
    public function showConfirmation() { return view('onboarding::onboarding.confirmation'); }
}
