@extends('onboarding::layouts.app')

@section('content')
<div class="logo"><h1>{{ config('onboarding.brand_name') }}</h1></div>
<div style="text-align: center; padding: 20px 0;">
    <div style="width: 60px; height: 60px; background: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; color: white; font-size: 30px;">✓</div>
    <h2>Onboarding démarré !</h2>
    <p>Vérifiez vos emails pour la suite.</p>
    <br>
    <a href="{{ route('onboarding.welcome') }}" class="btn">Retour</a>
</div>
@endsection
