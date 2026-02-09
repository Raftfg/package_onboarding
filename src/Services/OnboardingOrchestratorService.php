<?php

namespace Raftfg\OnboardingPackage\Services;

use Raftfg\OnboardingPackage\Models\Application;
use Raftfg\OnboardingPackage\Models\ApiKey;
use Raftfg\OnboardingPackage\Models\OnboardingRegistration;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class OnboardingOrchestratorService
{
    public function __construct(
        protected SubdomainService $subdomainService,
        protected OrganizationNameGenerator $organizationNameGenerator
    ) {}

    public function start(Application $application, string $email, ?string $orgName = null): OnboardingRegistration
    {
        $orgName = $this->organizationNameGenerator->generate('auto', ['email' => $email, 'organization_name' => $orgName]);
        $subdomain = $this->subdomainService->generateUniqueSubdomain($orgName, $email);

        return OnboardingRegistration::create([
            'application_id' => $application->id,
            'email' => $email,
            'organization_name' => $orgName,
            'subdomain' => $subdomain,
            'status' => 'pending',
            'metadata' => []
        ]);
    }

    public function provision(Application $application, string $uuid, bool $genKey = false): array
    {
        $reg = OnboardingRegistration::where('uuid', $uuid)->where('application_id', $application->id)->firstOrFail();

        $dns = $this->subdomainService->configureDNS($reg->subdomain);
        $ssl = $this->subdomainService->configureSSL($reg->subdomain);

        $reg->update([
            'status' => ($dns && $ssl) ? 'activated' : 'failed',
            'dns_configured' => $dns,
            'ssl_configured' => $ssl,
            'provisioning_attempts' => ($reg->provisioning_attempts ?? 0) + 1
        ]);

        $apiKey = null;
        if ($genKey && !$reg->api_key) {
            $res = ApiKey::generate('Onboarding - ' . $reg->subdomain, ['application_id' => $application->id]);
            $apiKey = $res['key'];
            $reg->update(['api_key' => $res['key_prefix'], 'api_secret' => Hash::make($apiKey)]);
        }

        return ['registration' => $reg->fresh(), 'api_key_plain' => $apiKey, 'is_idempotent' => false];
    }
}
