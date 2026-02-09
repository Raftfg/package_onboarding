<aside class="sidebar">
    <div class="sidebar-header">
        <h1>Onboarding</h1>
    </div>
    <nav class="sidebar-nav">
        <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
            Tableau de Bord
        </a>
        <a href="{{ route('onboarding.start') }}" class="nav-item">
            Nouvel Onboarding
        </a>
    </nav>
</aside>
