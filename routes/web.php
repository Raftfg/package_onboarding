<?php

use Raftfg\OnboardingPackage\Http\Controllers\Web\OnboardingController;
use Illuminate\Support\Facades\Route;

Route::get('/onboarding/start', [OnboardingController::class, 'showInitialForm'])->name('onboarding.start');
Route::post('/onboarding/start', [OnboardingController::class, 'storeInitialData'])->name('onboarding.storeInitialData');
Route::get('/onboarding/loading', [OnboardingController::class, 'showLoading'])->name('onboarding.loading');
Route::get('/onboarding/confirmation', [OnboardingController::class, 'showConfirmation'])->name('onboarding.confirmation');
