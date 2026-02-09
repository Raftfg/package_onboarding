<?php

namespace Raftfg\OnboardingPackage\Services;

class OrganizationNameGenerator
{
    public function generate(string $strategy, array $context = []): string
    {
        if (isset($context['organization_name']) && !empty($context['organization_name'])) {
            return $context['organization_name'];
        }
        return 'Org-' . substr(md5($context['email'] ?? uniqid()), 0, 8);
    }
}
