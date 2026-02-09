<?php

namespace Raftfg\OnboardingPackage\Services;

use Raftfg\OnboardingPackage\Models\Activity;
use Raftfg\OnboardingPackage\Models\Notification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardService
{
    public function getStats(): array
    {
        return Cache::remember('dashboard_stats', 300, function () {
            $userModel = config('auth.providers.users.model');
            $totalUsers = $userModel::count();
            $activeUsers = $userModel::where('status', 'active')->count();
            $todayActivities = Activity::whereDate('created_at', today())->count();
            $unreadNotifications = Notification::unread()->count();
            $recentActivities = Activity::recent(5)->count();
            
            $activitiesByType = Activity::where('created_at', '>=', now()->subDays(7))
                ->select('type', DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray();
            
            return [
                'total_users' => $totalUsers,
                'active_users' => $activeUsers,
                'today_activities' => $todayActivities,
                'unread_notifications' => $unreadNotifications,
                'recent_activities' => $recentActivities,
                'activities_by_type' => $activitiesByType,
            ];
        });
    }

    public function getRecentActivities(int $limit = 10)
    {
        return Activity::with('user')
            ->recent($limit)
            ->get();
    }

    public function createActivity(int $userId, string $type, string $description, array $metadata = []): Activity
    {
        try {
            $activity = Activity::create([
                'user_id' => $userId,
                'type' => $type,
                'description' => $description,
                'metadata' => $metadata,
            ]);

            Cache::forget('dashboard_stats');

            return $activity;
        } catch (\Exception $e) {
            Log::error("Erreur package: " . $e->getMessage());
            throw $e;
        }
    }
}
