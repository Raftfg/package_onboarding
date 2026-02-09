<?php

namespace Raftfg\OnboardingPackage\Services;

use Raftfg\OnboardingPackage\Models\OnboardingActivation;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ActivationService
{
    protected $tenantService;

    public function __construct(TenantService $tenantService)
    {
        $this->tenantService = $tenantService;
    }

    /**
     * Crée un token d'activation et le stocke
     */
    public function createActivationToken(string $email, string $organizationName, string $subdomain = null, string $databaseName = null): string
    {
        // Supprimer TOUS les tokens existants pour ce sous-domaine (actifs, expirés ou activés)
        if ($subdomain) {
            $existingBySubdomain = OnboardingActivation::where('subdomain', $subdomain)->get();
            
            if ($existingBySubdomain->isNotEmpty()) {
                $count = $existingBySubdomain->count();
                Log::warning('Suppression des tokens d\'activation existants pour le sous-domaine', [
                    'subdomain' => $subdomain,
                    'count' => $count,
                ]);
                
                OnboardingActivation::where('subdomain', $subdomain)->delete();
            }
        }
        
        // Générer un token unique
        $token = Str::random(64);
        
        // Vérifier que le token n'existe pas déjà (très peu probable)
        while (OnboardingActivation::where('token', $token)->exists()) {
            $token = Str::random(64);
        }

        // Récupérer la durée d'expiration en jours depuis la config
        $expiresDays = config('onboarding.activation_token_expires_days', 7);
        $expiresAt = Carbon::now()->addDays($expiresDays);
        
        // Créer l'enregistrement d'activation
        OnboardingActivation::create([
            'email' => $email,
            'organization_name' => $organizationName,
            'token' => $token,
            'subdomain' => $subdomain,
            'database_name' => $databaseName,
            'expires_at' => $expiresAt,
        ]);

        Log::info('Token d\'activation créé', [
            'email' => $email,
            'organization' => $organizationName,
            'expires_at' => $expiresAt->toDateTimeString(),
            'expires_in_days' => $expiresDays,
        ]);

        return $token;
    }

    /**
     * Valide un token d'activation
     */
    public function validateToken(string $token): bool
    {
        $activation = $this->getActivationByToken($token);
        
        if (!$activation) {
            return false;
        }

        return $activation->isValid();
    }

    /**
     * Récupère une activation par son token
     */
    public function getActivationByToken(string $token): ?OnboardingActivation
    {
        return OnboardingActivation::where('token', $token)->first();
    }

    /**
     * Active le compte en créant l'utilisateur admin
     */
    public function activateAccount(string $token, string $password): array
    {
        $activation = $this->getActivationByToken($token);

        if (!$activation) {
            throw new \Exception('Token d\'activation invalide');
        }

        if (!$activation->isValid()) {
            if ($activation->isExpired()) {
                $expiresInDays = config('onboarding.activation_token_expires_days', 7);
                throw new \Exception("Le lien d'activation a expiré (valable {$expiresInDays} " . ($expiresInDays > 1 ? 'jours' : 'jour') . "). Veuillez demander un nouveau lien.");
            }
            if ($activation->isActivated()) {
                throw new \Exception('Ce compte a déjà été activé.');
            }
            throw new \Exception('Le lien d\'activation n\'est plus valide.');
        }

        // Basculer vers la base de données du tenant
        if ($activation->database_name) {
            $this->tenantService->switchToTenantDatabase($activation->database_name);
        } else {
            throw new \Exception('Base de données du tenant non trouvée.');
        }

        try {
            // Récupérer le modèle User de l'application hôte
            $userModel = config('onboarding.user_model', 'App\Models\User');
            
            // Vérifier si l'utilisateur existe déjà (créé lors de l'onboarding)
            $existingUser = $userModel::where('email', $activation->email)->first();
            
            if ($existingUser) {
                // Mettre à jour le mot de passe
                $existingUser->update([
                    'password' => Hash::make($password),
                    'password_changed_at' => now(),
                ]);
                $user = $existingUser;
                
                Log::info('Mot de passe mis à jour pour utilisateur existant', [
                    'email' => $activation->email,
                    'user_id' => $user->id,
                ]);
            } else {
                // Créer l'utilisateur administrateur
                $user = $userModel::create([
                    'name' => $activation->organization_name,
                    'email' => $activation->email,
                    'password' => Hash::make($password),
                    'email_verified_at' => now(),
                    'password_changed_at' => now(),
                    'role' => 'admin',
                    'status' => 'active',
                ]);

                Log::info('Utilisateur créé', [
                    'email' => $activation->email,
                    'user_id' => $user->id,
                ]);
            }

            // Marquer l'activation comme complétée
            $activation->markAsActivated();

            Log::info('Compte activé avec succès', [
                'email' => $activation->email,
                'user_id' => $user->id,
                'subdomain' => $activation->subdomain,
                'database' => $activation->database_name,
            ]);

            // Retourner les informations nécessaires pour la connexion
            return [
                'user' => $user,
                'subdomain' => $activation->subdomain,
                'email' => $activation->email,
                'database_name' => $activation->database_name,
            ];
        } catch (\Exception $e) {
            Log::error('Erreur lors de l\'activation du compte', [
                'token' => $token,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }
}
