<?php

namespace Raftfg\OnboardingPackage\Http\Controllers\Api;

use Illuminate\Routing\Controller;
use Illuminate\Http\Request;
use Raftfg\OnboardingPackage\Services\OnboardingOrchestratorService;
use Raftfg\OnboardingPackage\Services\SubdomainService;

class OnboardingController extends Controller
{
    public function __construct(protected OnboardingOrchestratorService $service) {}

    public function start(Request $request)
    {
        $app = $request->get('application');
        $validated = $request->validate([
            'email' => 'required|email',
            'organization_name' => 'nullable|string',
        ]);

        $reg = $this->service->start($app, $validated['email'], $validated['organization_name'] ?? null);
        
        return response()->json([
            'success' => true,
            'uuid' => $reg->uuid,
            'subdomain' => $reg->subdomain,
            'full_domain' => $reg->subdomain . '.' . config('onboarding.brand_domain'),
            'metadata' => [
                'created_at' => $reg->created_at->toIso8601String(),
                'infrastructure_status' => 'pending'
            ]
        ], 201);
    }

    public function provision(Request $request)
    {
        $app = $request->get('application');
        $validated = $request->validate(['uuid' => 'required|string', 'generate_api_key' => 'nullable|boolean']);

        $res = $this->service->provision($app, $validated['uuid'], (bool)($validated['generate_api_key'] ?? false));
        $reg = $res['registration'];

        return response()->json([
            'success' => true,
            'uuid' => $reg->uuid,
            'status' => $reg->status,
            'api_key' => $res['api_key_plain'],
            'metadata' => [
                'dns_configured' => $reg->dns_configured,
                'ssl_configured' => $reg->ssl_configured,
                'provisioning_attempts' => $reg->provisioning_attempts
            ]
        ]);
    }

    public function status($uuid)
    {
        $reg = \Raftfg\OnboardingPackage\Models\OnboardingRegistration::where('uuid', $uuid)->firstOrFail();
        return response()->json(['success' => true, 'status' => $reg->status, 'subdomain' => $reg->subdomain]);
    }
}
