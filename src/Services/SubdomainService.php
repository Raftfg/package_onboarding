<?php

namespace Raftfg\OnboardingPackage\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Raftfg\OnboardingPackage\Models\OnboardingRegistration;

class SubdomainService
{
    private const RESERVED = ['www', 'admin', 'api', 'app', 'test', 'local'];

    public function generateUniqueSubdomain(string $name, string $email): string
    {
        $base = Str::slug($name ?: explode('@', $email)[0], '-');
        $subdomain = $base;
        $counter = 1;

        while (in_array($subdomain, self::RESERVED) || OnboardingRegistration::where('subdomain', $subdomain)->exists()) {
            $subdomain = $base . '-' . $counter++;
        }

        return $subdomain;
    }

    public function getSubdomainUrl(string $subdomain): string
    {
        $base = config('onboarding.brand_domain', 'akasigroup.local');
        return "https://{$subdomain}.{$base}";
    }

    public function configureDNS(string $subdomain): bool { return true; }
    public function configureSSL(string $subdomain): bool { return true; }
}
