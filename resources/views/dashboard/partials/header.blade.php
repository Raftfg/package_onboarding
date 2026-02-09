<header class="header">
    <div class="header-left">
        <strong>{{ config('onboarding.brand_name', 'Raftfg') }}</strong>
    </div>
    <div class="header-right">
        <span>{{ Auth::user()->name ?? 'Invité' }}</span>
        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
            @csrf
            <button type="submit" style="background: none; border: none; color: #ef4444; cursor: pointer; text-decoration: underline;">Déconnexion</button>
        </form>
    </div>
</header>
