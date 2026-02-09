@extends('onboarding::layouts.app')

@section('content')
<div class="logo"><h1>{{ config('onboarding.brand_name') }}</h1></div>
<h2 style="text-align: center; margin-bottom: 20px;">Créer votre espace</h2>
<form method="POST" action="{{ route('onboarding.storeInitialData') }}">
    @csrf
    <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
    </div>
    <div class="form-group">
        <label>Organisation</label>
        <input type="text" name="organization_name">
    </div>
    <button type="submit" class="btn">Continuer</button>
</form>
@endsection
