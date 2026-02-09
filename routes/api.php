<?php

use Raftfg\OnboardingPackage\Http\Controllers\Api\OnboardingController;
use Illuminate\Support\Facades\Route;

Route::prefix('onboarding')->middleware(['master.key'])->group(function () {
    Route::post('/start', [OnboardingController::class, 'start'])
        ->middleware('rate.limit.onboarding:start')
        ->name('onboarding.api.v1.onboarding.start');
        
    Route::post('/provision', [OnboardingController::class, 'provision'])
        ->middleware('rate.limit.onboarding:provision')
        ->name('onboarding.api.v1.onboarding.provision');
        
    Route::get('/status/{uuid}', [OnboardingController::class, 'status'])
        ->middleware('rate.limit.onboarding:status')
        ->name('onboarding.api.v1.onboarding.status');
});
