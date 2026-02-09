@extends('onboarding::layouts.app')

@section('title', 'Chargement... - ' . config('onboarding.brand_name'))

@push('styles')
<style>
    .loading-container {
        text-align: center;
        padding: 40px 20px;
    }
    
    .loading-spinner-large {
        width: 80px;
        height: 80px;
        border: 6px solid rgba(0, 40, 111, 0.1);
        border-top-color: #00286f;
        border-radius: 50%;
        animation: spin 1s linear infinite;
        margin: 0 auto 30px;
    }
    
    .loading-message {
        font-size: 18px;
        color: #333;
        margin-bottom: 15px;
        min-height: 30px;
        transition: opacity 0.3s ease;
    }
    
    .loading-message.active {
        opacity: 1;
    }
    
    .loading-message.inactive {
        opacity: 0.3;
    }
    
    .progress-bar {
        width: 100%;
        height: 4px;
        background: #e0e0e0;
        border-radius: 2px;
        margin-top: 30px;
        overflow: hidden;
    }
    
    .progress-fill {
        height: 100%;
        background: #00286f;
        width: 0%;
        transition: width 0.5s ease;
        border-radius: 2px;
    }
    
    @keyframes spin {
        to { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')
<div class="logo">
    <h1>{{ config('onboarding.brand_name') }}</h1>
</div>

<div class="loading-container">
    <div class="loading-spinner-large"></div>
    
    <div id="messageContainer">
        <div class="loading-message active" id="message1">Chargement...</div>
        <div class="loading-message inactive" id="message2">Création de votre espace {{ config('onboarding.brand_name') }} en cours...</div>
        <div class="loading-message inactive" id="message3">Configuration de votre environnement…</div>
        <div class="loading-message inactive" id="message4">Finalisation…</div>
    </div>
    
    <div class="progress-bar">
        <div class="progress-fill" id="progressFill"></div>
    </div>
</div>

@push('scripts')
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(document).ready(function() {
        let currentMessage = 1;
        const totalMessages = 4;
        let progress = 0;
        
        // Show messages sequentially
        function showNextMessage() {
            if (currentMessage < totalMessages) {
                $(`#message${currentMessage}`).removeClass('active').addClass('inactive');
                currentMessage++;
                $(`#message${currentMessage}`).removeClass('inactive').addClass('active');
                
                // Update progress bar
                progress = (currentMessage / totalMessages) * 100;
                $('#progressFill').css('width', progress + '%');
            }
        }
        
        // Change message every 2 seconds
        const messageInterval = setInterval(showNextMessage, 2000);
        
        // Simulate loading and redirect to confirmation
        setTimeout(function() {
            clearInterval(messageInterval);
            $('#message4').removeClass('inactive').addClass('active');
            $('#progressFill').css('width', '100%');
            
            // Redirect to confirmation after a brief pause
            setTimeout(function() {
                window.location.href = '{{ route("onboarding.confirmation") }}';
            }, 500);
        }, 8000); // 8 seconds total
    });
</script>
@endpush
@endsection
