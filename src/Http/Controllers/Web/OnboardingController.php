<?php

namespace Raftfg\OnboardingPackage\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Raftfg\OnboardingPackage\Services\OnboardingOrchestratorService;
use Raftfg\OnboardingPackage\Models\Application;

class OnboardingController extends Controller
{
    public function __construct(protected OnboardingOrchestratorService $service) {}

    public function welcome() { return view('onboarding::onboarding.welcome'); }
    public function showInitialForm() { return view('onboarding::onboarding.initial-form'); }

    public function storeInitialData(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'organization_name' => 'nullable|string',
        ]);

        // S'assurer qu'une application par défaut existe pour le Web
        $app = Application::firstOrCreate(
            ['app_name' => 'default_web'],
            [
                'description' => 'Default Web Application',
                'master_key' => bcrypt('default_secret'),
                'status' => 'active'
            ]
        );

        // Démarrer l'onboarding
        $reg = $this->service->start($app, $validated['email'], $validated['organization_name'] ?? null);
        
        // Provisionner immédiatement (simulé pour le web)
        $this->service->provision($app, $reg->uuid);

        $token = Str::random(32);
        Cache::put("onboarding_data_{$token}", [
            'email' => $validated['email'],
            'organization_name' => $validated['organization_name'],
            'uuid' => $reg->uuid
        ], now()->addMinutes(10));
        
        Session::put('onboarding_data', $validated);
        
        return redirect()->route('onboarding.loading', ['token' => $token]);
    }

    public function showLoading(Request $request) { return view('onboarding::onboarding.loading'); }
    
    public function showConfirmation() 
    { 
        $data = Session::get('onboarding_data', []);
        return view('onboarding::onboarding.confirmation', [
            'email' => $data['email'] ?? '',
            'organization_name' => $data['organization_name'] ?? ''
        ]); 
    }

    public function showActivation($token)
    {
        return view('onboarding::onboarding.activation', ['token' => $token]);
    }
}
