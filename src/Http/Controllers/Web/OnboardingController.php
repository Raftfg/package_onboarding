<?php

namespace Raftfg\OnboardingPackage\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Raftfg\OnboardingPackage\Services\OnboardingOrchestratorService;
use Raftfg\OnboardingPackage\Services\SubdomainService;
use Raftfg\OnboardingPackage\Services\TenantDatabaseService;
use Raftfg\OnboardingPackage\Services\ActivationService;
use Raftfg\OnboardingPackage\Services\TenantService;
use Raftfg\OnboardingPackage\Models\Application;
use Raftfg\OnboardingPackage\Models\OnboardingSession;
use Raftfg\OnboardingPackage\Mail\ActivationMail;

class OnboardingController extends Controller
{
    public function __construct(
        protected OnboardingOrchestratorService $orchestratorService,
        protected SubdomainService $subdomainService,
        protected TenantDatabaseService $tenantDatabaseService,
        protected ActivationService $activationService,
        protected TenantService $tenantService
    ) {}

    public function welcome() { return view('onboarding::onboarding.welcome'); }
    
    public function showInitialForm() { return view('onboarding::onboarding.initial-form'); }

    public function storeInitialData(Request $request)
    {
        $validated = $request->validate([
            'email' => 'required|email',
            'organization_name' => 'nullable|string',
        ]);

        try {
            // 1. Ensure default application exists
            $app = Application::firstOrCreate(
                ['app_name' => 'default_web'],
                [
                    'description' => 'Default Web Application',
                    'master_key' => bcrypt('default_secret'),
                    'status' => 'active'
                ]
            );

            // 2. Generate subdomain
            $subdomain = $this->subdomainService->generateUniqueSubdomain(
                $validated['organization_name'] ?? $validated['email'],
                $validated['email']
            );

            // 3. Create tenant database
            $databaseName = $this->tenantDatabaseService->createTenantDatabase($subdomain);

            // 4. Create onboarding session
            $session = OnboardingSession::create([
                'email' => $validated['email'],
                'organization_name' => $validated['organization_name'],
                'subdomain' => $subdomain,
                'database_name' => $databaseName,
                'status' => 'pending_activation',
                'metadata' => []
            ]);

            // 5. Create activation token
            $token = $this->activationService->createActivationToken(
                $validated['email'],
                $validated['organization_name'] ?? $validated['email'],
                $subdomain,
                $databaseName
            );

            // 6. Send activation email
            if (config('onboarding.email.send_activation', true)) {
                $activationUrl = url("/onboarding/activate/{$token}");
                
                Mail::to($validated['email'])->send(new ActivationMail(
                    $validated['email'],
                    $validated['organization_name'] ?? $validated['email'],
                    $activationUrl,
                    config('onboarding.activation.token_expires_days', 7)
                ));

                Log::info('Activation email sent', [
                    'email' => $validated['email'],
                    'subdomain' => $subdomain,
                ]);
            }

            // 7. Store data in session for confirmation page
            Session::put('onboarding_data', $validated);
            
            return redirect()->route('onboarding.loading');
        } catch (\Exception $e) {
            Log::error('Onboarding error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return redirect()->back()
                ->withErrors(['error' => 'Une erreur est survenue. Veuillez réessayer.'])
                ->withInput();
        }
    }

    public function showLoading(Request $request) { 
        return view('onboarding::onboarding.loading'); 
    }
    
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
        // Validate token
        if (!$this->activationService->validateToken($token)) {
            $activation = $this->activationService->getActivationByToken($token);
            
            if ($activation && $activation->isActivated()) {
                return redirect()->route('login')
                    ->with('info', 'Ce compte a déjà été activé. Veuillez vous connecter.');
            }
            
            if ($activation && $activation->isExpired()) {
                return view('onboarding::onboarding.activation-expired');
            }
            
            return view('onboarding::onboarding.activation-invalid');
        }

        // Get activation details
        $activation = $this->activationService->getActivationByToken($token);
        
        return view('onboarding::onboarding.activation', [
            'token' => $token,
            'email' => $activation->email,
            'organizationName' => $activation->organization_name,
        ]);
    }

    public function activate(Request $request)
    {
        $validated = $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        try {
            // Activate the account
            $result = $this->activationService->activateAccount(
                $validated['token'],
                $validated['password']
            );

            if ($result['subdomain'] && $result['user']) {
                $subdomain = $result['subdomain'];
                
                // Switch to tenant database
                if ($result['database_name']) {
                    $this->tenantService->switchToTenantDatabase($result['database_name']);
                }
                
                // Log the user in
                Auth::login($result['user']);
                $request->session()->regenerate();

                // Create auto-login token for 30 minutes
                $autoLoginToken = Str::random(64);
                
                DB::connection(config('database.default'))->table('auto_login_tokens')->insert([
                    'token' => $autoLoginToken,
                    'user_id' => $result['user']->id,
                    'subdomain' => $subdomain,
                    'database_name' => $result['database_name'],
                    'expires_at' => now()->addMinutes(30),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Construct dashboard URL
                if (config('app.env') === 'local') {
                    $port = $request->getPort();
                    $dashboardUrl = "http://{$subdomain}.localhost:{$port}/dashboard?auto_login_token={$autoLoginToken}";
                } else {
                    $baseDomain = config('onboarding.brand_domain');
                    $dashboardUrl = "https://{$subdomain}.{$baseDomain}/dashboard?auto_login_token={$autoLoginToken}";
                }

                return redirect()->away($dashboardUrl)
                    ->with('success', 'Votre compte a été activé avec succès !');
            } else {
                throw new \Exception('Erreur lors de l\'activation : données incomplètes');
            }
        } catch (\Exception $e) {
            Log::error('Activation error', [
                'error' => $e->getMessage(),
                'email' => $validated['email']
            ]);
            
            $userMessage = 'Une erreur est survenue lors de l\'activation de votre compte.';
            
            if (str_contains($e->getMessage(), 'invalide')) {
                $userMessage = $e->getMessage();
            } elseif (str_contains($e->getMessage(), 'expiré')) {
                $userMessage = $e->getMessage();
            } elseif (str_contains($e->getMessage(), 'déjà été activé')) {
                $userMessage = $e->getMessage();
            }
            
            return back()->withErrors(['activation' => $userMessage])->withInput();
        }
    }
}
