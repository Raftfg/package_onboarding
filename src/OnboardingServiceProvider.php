<?php

namespace Raftfg\OnboardingPackage;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Routing\Router;

class OnboardingServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/Config/onboarding.php',
            'onboarding'
        );
    }

    public function boot(): void
    {
        if (is_dir(__DIR__ . '/../resources/views')) {
            $this->loadViewsFrom(__DIR__ . '/../resources/views', 'onboarding');
        }

        $this->publishes([
            __DIR__ . '/Config/onboarding.php' => config_path('onboarding.php'),
        ], 'onboarding-config');

        $this->publishes([
            __DIR__ . '/Database/Migrations' => database_path('migrations'),
        ], 'onboarding-migrations');

        $this->registerRoutes();
    }

    protected function registerRoutes(): void
    {
        Route::middleware('api')
            ->prefix(config('onboarding.api_prefix', 'api/v1'))
            ->group(function () {
                $this->loadRoutesFrom(__DIR__ . '/../routes/api.php');
            });

        if (file_exists(__DIR__ . '/../routes/web.php')) {
            Route::middleware('web')
                ->group(function () {
                    $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
                });
        }
    }
}
