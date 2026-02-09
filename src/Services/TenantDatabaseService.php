<?php

namespace Raftfg\OnboardingPackage\Services;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Artisan;

class TenantDatabaseService
{
    /**
     * Create a new tenant database
     */
    public function createTenantDatabase(string $subdomain): string
    {
        $prefix = config('onboarding.tenant_database.prefix', 'tenant_');
        $databaseName = $prefix . $subdomain;
        
        // Sanitize database name (only alphanumeric and underscores)
        $databaseName = preg_replace('/[^a-zA-Z0-9_]/', '_', $databaseName);
        
        try {
            // Create database
            DB::statement("CREATE DATABASE IF NOT EXISTS `{$databaseName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            
            Log::info('Tenant database created', [
                'subdomain' => $subdomain,
                'database' => $databaseName,
            ]);
            
            // Run migrations if auto-migrate is enabled
            if (config('onboarding.tenant_database.auto_migrate', true)) {
                $this->runMigrations($databaseName);
            }
            
            return $databaseName;
        } catch (\Exception $e) {
            Log::error('Failed to create tenant database', [
                'subdomain' => $subdomain,
                'database' => $databaseName,
                'error' => $e->getMessage(),
            ]);
            throw new \Exception("Erreur lors de la création de la base de données tenant: " . $e->getMessage());
        }
    }

    /**
     * Run migrations on a tenant database
     */
    public function runMigrations(string $databaseName): void
    {
        try {
            // Configure temporary tenant connection
            $defaultConfig = config('database.connections.mysql');
            
            config(['database.connections.tenant_temp' => [
                'driver' => 'mysql',
                'host' => $defaultConfig['host'],
                'port' => $defaultConfig['port'],
                'database' => $databaseName,
                'username' => $defaultConfig['username'],
                'password' => $defaultConfig['password'],
                'charset' => 'utf8mb4',
                'collation' => 'utf8mb4_unicode_ci',
                'prefix' => '',
                'strict' => true,
                'engine' => null,
            ]]);

            DB::purge('tenant_temp');

            // Run migrations
            Artisan::call('migrate', [
                '--database' => 'tenant_temp',
                '--force' => true,
            ]);

            Log::info('Migrations run on tenant database', ['database' => $databaseName]);

            // Clean up
            DB::purge('tenant_temp');
        } catch (\Exception $e) {
            Log::error('Failed to run migrations on tenant database', [
                'database' => $databaseName,
                'error' => $e->getMessage(),
            ]);
            // Don't throw - database was created successfully
        }
    }

    /**
     * Check if a tenant database exists
     */
    public function databaseExists(string $databaseName): bool
    {
        try {
            $result = DB::select("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = ?", [$databaseName]);
            return !empty($result);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Delete a tenant database (use with caution!)
     */
    public function deleteTenantDatabase(string $databaseName): bool
    {
        try {
            DB::statement("DROP DATABASE IF EXISTS `{$databaseName}`");
            
            Log::warning('Tenant database deleted', ['database' => $databaseName]);
            
            return true;
        } catch (\Exception $e) {
            Log::error('Failed to delete tenant database', [
                'database' => $databaseName,
                'error' => $e->getMessage(),
            ]);
            return false;
        }
    }
}
