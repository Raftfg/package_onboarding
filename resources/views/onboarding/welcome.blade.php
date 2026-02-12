@extends('onboarding::layouts.app')

@section('title', 'Bienvenue sur ' . config('onboarding.brand_name'))

@section('content')
<div class="logo">
    <h1>{{ config('onboarding.brand_name') }}</h1>
</div>

<div class="welcome-message">
    <h2>Bienvenue sur {{ config('onboarding.brand_name') }}</h2>
    <p>Nous sommes ravis de vous accueillir ! Commençons par configurer votre compte.</p>
</div>

<div style="display: flex; flex-direction: column; gap: 15px;">
    <a href="{{ route('onboarding.start') }}" class="btn btn-primary">
        Démarrer
    </a>
    
</div>
@endsection
