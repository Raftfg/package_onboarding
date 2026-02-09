@extends('onboarding::layouts.app')

@section('content')
<div class="logo"><h1>{{ config('onboarding.brand_name') }}</h1></div>
<div style="text-align: center; margin-bottom: 20px;">
    <h2>Bienvenue</h2>
    <p>Prêt à créer votre espace ?</p>
</div>
<a href="{{ route('onboarding.start') }}" class="btn">Commencer</a>
@endsection
