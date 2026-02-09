<?php

namespace Raftfg\OnboardingPackage\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TenantService
{
    /**
     * Récupère le nom de la base de données pour un sous-domaine donné
     */
    public function getTenantDatabase(string $subdomain): ?string
    {
        $cacheKey = "tenant_db_{$subdomain}";
        
        return Cache::remember($cacheKey, 3600, function () use ($subdomain) {
            // Utiliser Query Builder pour éviter les problèmes de modèle
            $sessionModel = config('onboarding.session_model', 'App\Models\OnboardingSession');
            $defaultConnection = config('database.default');
            
            $onboarding = DB::connection($defaultConnection)
                ->table('onboarding_sessions')
                ->where('subdomain', $subdomain)
                ->whereIn('status', ['completed', 'pending_activation'])
                ->first();
            
            return $onboarding ? $onboarding->database_name : null;
        });
    }

    /**
     * Configure la connexion de base de données pour un tenant spécifique
     */
    public function switchToTenantDatabase(string $databaseName): void
    {
        if (empty($databaseName)) {
            throw new \Exception('Nom de base de données invalide');
        }

        // Récupérer la configuration de base
        $defaultConfig = config('database.connections.mysql');
        
        // Créer une nouvelle connexion dynamique pour ce tenant
        Config::set("database.connections.tenant", [
            'driver' => 'mysql',
            'host' => $defaultConfig['host'],
            'port' => $defaultConfig['port'],
            'database' => $databaseName,
            'username' => $defaultConfig['username'],
            'password' => $defaultConfig['password'],
            'charset' => $defaultConfig['charset'],
            'collation' => $defaultConfig['collation'],
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => $defaultConfig['strict'],
            'engine' => null,
        ]);

        // Basculer la connexion par défaut vers le tenant
        DB::purge('tenant');
        Config::set('database.default', 'tenant');
    }

    /**
     * Vérifie si un tenant existe et est actif
     */
    public function tenantExists(string $subdomain): bool
    {
        $defaultConnection = config('database.default');
        
        return DB::connection($defaultConnection)
            ->table('onboarding_sessions')
            ->where('subdomain', $subdomain)
            ->whereIn('status', ['completed', 'pending_activation'])
            ->exists();
    }

    /**
     * Nettoie le cache d'un tenant
     */
    public function clearTenantCache(string $subdomain): void
    {
        Cache::forget("tenant_db_{$subdomain}");
    }
}
