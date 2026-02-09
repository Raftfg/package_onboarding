@extends('onboarding::layouts.dashboard')

@section('title', 'Dashboard')

@section('content')
    <div style="margin-bottom: 30px;">
        <h1 style="font-size: 28px; font-weight: 600; margin-bottom: 10px;">
            Bienvenue !
        </h1>
        <p style="color: #666; font-size: 16px;">
            Voici un aperçu de votre activité d'onboarding.
        </p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <div class="card">
            <h3 style="font-size: 14px; color: #666; text-transform: uppercase;">Utilisateurs Active</h3>
            <div style="font-size: 32px; font-weight: 600;">{{ $stats['active_users'] ?? 0 }}</div>
        </div>
        <div class="card">
            <h3 style="font-size: 14px; color: #666; text-transform: uppercase;">Activités du Jour</h3>
            <div style="font-size: 32px; font-weight: 600;">{{ $stats['today_activities'] ?? 0 }}</div>
        </div>
        <div class="card">
            <h3 style="font-size: 14px; color: #666; text-transform: uppercase;">Total Utilisateurs</h3>
            <div style="font-size: 32px; font-weight: 600;">{{ $stats['total_users'] ?? 0 }}</div>
        </div>
    </div>

    <div class="card">
        <h3 class="card-title">Activités Récentes</h3>
        <ul style="list-style: none; padding: 0;">
            @forelse($activities ?? [] as $activity)
                <li style="padding: 10px 0; border-bottom: 1px solid #eee;">
                    <strong>{{ $activity->user->name ?? 'Système' }}</strong>: {{ $activity->description }}
                    <br><small style="color: #999;">{{ $activity->created_at->diffForHumans() }}</small>
                </li>
            @empty
                <li style="padding: 20px; text-align: center; color: #999;">Aucune activité récente.</li>
            @endforelse
        </ul>
    </div>
@endsection
