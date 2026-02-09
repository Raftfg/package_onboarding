<?php

use Raftfg\OnboardingPackage\Http\Controllers\Web\OnboardingController;
use Raftfg\OnboardingPackage\Http\Controllers\Web\DashboardController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    // Onboarding publique
    Route::get('/onboarding', [OnboardingController::class, 'welcome'])->name('onboarding.welcome');
    Route::get('/onboarding/start', [OnboardingController::class, 'showInitialForm'])->name('onboarding.start');
    Route::post('/onboarding/start', [OnboardingController::class, 'storeInitialData'])->name('onboarding.storeInitialData');
    Route::get('/onboarding/loading', [OnboardingController::class, 'showLoading'])->name('onboarding.loading');
    Route::get('/onboarding/confirmation', [OnboardingController::class, 'showConfirmation'])->name('onboarding.confirmation');
    
    // Activation
    Route::get('/onboarding/activate/{token}', [OnboardingController::class, 'showActivation'])->name('onboarding.activation');
    
    // Dashboard (protégé par auth)
    Route::middleware(['auth'])->group(function () {
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::post('/dashboard/resend-activation', [DashboardController::class, 'resendActivationEmail'])->name('dashboard.resend-activation');
    });
});
