<?php

namespace Raftfg\OnboardingPackage\Http\Controllers\Web;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Raftfg\OnboardingPackage\Services\DashboardService;

class DashboardController extends Controller
{
    public function __construct(protected DashboardService $dashboardService) {}

    public function index(Request $request)
    {
        $stats = $this->dashboardService->getStats();
        $activities = $this->dashboardService->getRecentActivities(5);
        
        return view('onboarding::dashboard.index', [
            'stats' => $stats,
            'activities' => $activities,
        ]);
    }

    public function resendActivationEmail(Request $request)
    {
        // Logique de renvoi d'email simplifiée pour le package
        return response()->json(['success' => true, 'message' => 'Email renvoyé (en théorie)']);
    }
}
