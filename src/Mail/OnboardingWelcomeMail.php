<?php

namespace Raftfg\OnboardingPackage\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OnboardingWelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public $adminData;
    public $subdomain;
    public $url;

    public function __construct(array $adminData, string $subdomain, string $url)
    {
        $this->adminData = $adminData;
        $this->subdomain = $subdomain;
        $this->url = $url;
    }

    public function build()
    {
        $adminName = trim(($this->adminData['admin_first_name'] ?? '') . ' ' . ($this->adminData['admin_last_name'] ?? ''));
        if (empty($adminName)) {
            $adminName = $this->adminData['admin_email'] ?? 'Utilisateur';
        }
        
        return $this->subject('[' . config('onboarding.brand_name', 'Raftfg') . '] Bienvenue dans votre nouvel espace')
                    ->view('onboarding::emails.onboarding-welcome')
                    ->with([
                        'adminName' => $adminName,
                        'subdomain' => $this->subdomain,
                        'url' => $this->url,
                        'adminEmail' => $this->adminData['admin_email'] ?? '',
                    ]);
    }
}
