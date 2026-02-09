@extends('onboarding::layouts.app')

@section('content')
<div class="logo"><h1>{{ config('onboarding.brand_name') }}</h1></div>
<div style="text-align: center; padding: 40px 0;">
    <div style="width: 50px; height: 50px; border: 4px solid #f3f3f3; border-top: 4px solid #667eea; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 20px;"></div>
    <h2>Configuration en cours...</h2>
    <p>Veuillez patienter.</p>
</div>
<style> @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } } </style>
<script>
    setTimeout(function() {
        window.location.href = '{{ route("onboarding.confirmation") }}';
    }, 3000);
</script>
@endsection
