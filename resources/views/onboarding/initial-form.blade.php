@extends('onboarding::layouts.app')

@section('title', 'Créer votre espace - ' . config('onboarding.brand_name'))

@push('styles')
<style>
    .form-group {
        margin-bottom: 18px !important;
    }
    
    .form-group input {
        padding: 11px 15px !important;
        font-size: 15px !important;
        height: auto !important;
        border: 2px solid #e0e0e0 !important;
    }
    
    .form-group input:focus {
        border-color: #00286f !important;
        box-shadow: 0 0 0 3px rgba(0, 40, 111, 0.1) !important;
    }
    
    .form-group label {
        font-size: 13px !important;
        margin-bottom: 7px !important;
        font-weight: 500 !important;
    }
    
    .welcome-message {
        margin-bottom: 28px;
    }
    
    .welcome-message h2 {
        font-size: 1.8rem !important;
        margin-bottom: 10px !important;
    }
    
    .welcome-message p {
        font-size: 14px !important;
        color: #666 !important;
        line-height: 1.5 !important;
    }
    
    #submitBtn {
        margin-top: 25px !important;
        padding: 13px !important;
        font-size: 15px !important;
        font-weight: 600 !important;
    }
</style>
@endpush

@section('content')
<div class="logo">
    <h1>{{ config('onboarding.brand_name') }}</h1>
</div>

<div class="welcome-message">
    <h2>Créer votre espace</h2>
    <p>Remplissez les informations ci-dessous pour commencer</p>
</div>

@if(session('error'))
    <div class="error-message" style="background: #fee2e2; color: #991b1b; padding: 12px 20px; border-radius: 8px; margin-bottom: 20px; text-align: center;">
        {{ session('error') }}
    </div>
@endif

<form method="POST" action="{{ route('onboarding.storeInitialData') }}" id="initialForm">
    @csrf
    
    <div class="form-group">
        <label for="email">Adresse e-mail *</label>
        <input type="email" id="email" name="email" required 
               value="{{ old('email') }}" placeholder="votre@email.com" autocomplete="email">
        @error('email')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-group">
        <label for="organization_name">Nom de votre organisation</label>
        <input type="text" id="organization_name" name="organization_name" 
               value="{{ old('organization_name') }}" placeholder="Ex: Mon Organisation">
        @error('organization_name')
            <div class="error-message">{{ $message }}</div>
        @enderror
    </div>

    <button type="submit" class="btn btn-primary" id="submitBtn">
        Continuer
    </button>
</form>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const form = document.getElementById('initialForm');
        const submitBtn = document.getElementById('submitBtn');
        
        if (form) {
            form.addEventListener('submit', function(e) {
                // Disable button to prevent double submission
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Traitement...';
                }
            });
        }
        
        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Erreur',
                text: "{{ session('error') }}",
                confirmButtonColor: '#00286f'
            });
        @endif
    });
</script>
@endpush
@endsection
