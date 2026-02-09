<?php

namespace Raftfg\OnboardingPackage\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ActivationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $email;
    public $organizationName;
    public $activationToken;
    public $activationUrl;
    public $expiresInDays;

    public function __construct(string $email, string $organizationName, string $activationToken)
    {
        $this->email = $email;
        $this->organizationName = $organizationName;
        $this->activationToken = $activationToken;
        $this->expiresInDays = config('onboarding.activation_token_expires_days', 7);
        
        $this->activationUrl = route('onboarding.activation', [
            'token' => $activationToken,
            'email' => $email
        ]);
    }

    public function build()
    {
        return $this->subject('[' . config('onboarding.brand_name', 'Raftfg') . '] Activation de votre espace')
                    ->view('onboarding::emails.activation')
                    ->with([
                        'email' => $this->email,
                        'organizationName' => $this->organizationName,
                        'activationUrl' => $this->activationUrl,
                        'expiresInDays' => $this->expiresInDays,
                    ]);
    }
}
