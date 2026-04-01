@extends('layouts.app')

<style>
    /* 1. Main Input Styling */
    .form-control {
        width: 100% !important;
        /* Semi-transparent dark background so white text stands out */
        background: rgba(255, 255, 255, 0.1) !important; 
        border: 2px solid rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 16px;
        color: #ffffff !important; /* Forces text to white */
        transition: all 0.3s ease-in-out;
    }

    /* 2. Placeholder text color (light white) */
    .form-control::placeholder {
        color: rgba(255, 255, 255, 0.5) !important;
    }

    /* 3. Input Focus State */
    .form-control:focus {
        background-color: rgba(255, 255, 255, 0.15) !important;
        border-color: #818cf8 !important;
        box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.25) !important;
        color: #ffffff !important;
        outline: none;
    }

    /* 4. Ensure labels and helper text are white */
    .form-label, .text-white, .fw-bold {
        color: #ffffff !important;
    }

    /* 5. Muted text (like "Remember me") to light grey/white */
    .text-muted, .text-secondary, .text-white-50 {
        color: rgba(255, 255, 255, 0.7) !important;
    }

    /* 6. Card background (Glassmorphism effect) */
    .card {
        background: rgba(0, 0, 0, 0.6) !important; /* Dark tint */
        backdrop-filter: blur(10px); /* Blurs the background image behind the card */
        border: 1px solid rgba(255, 255, 255, 0.1) !important;
    }

    /* 7. Button Styling */
    .main-btn {
        background-color: #4f46e5;
        border: none;
        border-radius: 12px;
        color: #ffffff;
        transition: all 0.3s ease;
    }

    .main-btn:hover {
        background-color: #4338ca !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(79, 70, 229, 0.4);
    }

    /* 8. Link color for visibility */
    a.text-decoration-none {
        color: #818cf8 !important;
    }
</style>

@section('content')

<div class="d-flex justify-content-center align-items-center min-vh-100 py-5" 
     style="background: url('{{ asset('path-to-your-image.jpg') }}') no-repeat center center; background-size: cover;">
    
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 480px; width: 100%; background: rgba(0, 0, 0, 0.5); backdrop-filter: blur(10px);">
        
        <div style="height: 6px; background-color: #4f46e5; border-radius: 4px 4px 0 0;"></div>

        <div class="card-body p-5 d-flex flex-column align-items-center text-center">
            
            <div class="mb-4">
                <span class="p-3 rounded-circle bg-white d-inline-block shadow-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#4f46e5" class="bi bi-box-arrow-in-right" viewBox="0 0 16 16">
                        <path fill-rule="evenodd" d="M6 3.5a.5.5 0 0 1 .5-.5h8a.5.5 0 0 1 .5.5v9a.5.5 0 0 1-.5.5h-8a.5.5 0 0 1-.5-.5v-2a.5.5 0 0 0-1 0v2A1.5 1.5 0 0 0 6.5 14h8a1.5 1.5 0 0 0 1.5-1.5v-9A1.5 1.5 0 0 0 14.5 2h-8A1.5 1.5 0 0 0 5 3.5v2a.5.5 0 0 0 1 0v-2z"/>
                        <path fill-rule="evenodd" d="M11.854 8.354a.5.5 0 0 0 0-.708l-3-3a.5.5 0 1 0-.708.708L10.293 7.5H1.5a.5.5 0 0 0 0 1h8.793l-2.147 2.146a.5.5 0 0 0 .708.708l3-3z"/>
                    </svg>
                </span>
                <h2 class="fw-bold text-white mt-3">Welcome Back</h2>
                <p class="text-white-50 small">Log in to manage your account</p>
            </div>

            @if($errors->any())
                <div class="alert alert-danger w-100 py-2 small" style="border-radius: 10px;">
                    {{ $errors->first() }}
                </div>
            @endif

            <form action="/login" method="POST" class="w-100">
                @csrf
                
                <div class="mb-3 text-start">
                    <label class="form-label small fw-bold text-white">EMAIL ADDRESS</label>
                    <input type="email" name="email" class="form-control" placeholder="name@example.com" value="{{ old('email') }}" required autofocus>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label small fw-bold text-white">PASSWORD</label>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="remember" id="remember">
                        <label class="form-check-label small text-white-50" for="remember">Remember me</label>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-lg text-white fw-bold shadow-sm main-btn" style="padding: 12px;">
                        Sign In
                    </button>
                </div>
            </form>

            <div class="mt-4 pt-2">
                <p class="text-white-50 small">Don't have an account? <a href="/register" class="text-decoration-none fw-bold" style="color: #818cf8;">Sign Up</a></p>
            </div>
        </div>
    </div>
</div>

<style>
    /* Focus and Hover states */
    .form-control:focus {
        background-color: rgba(255, 255, 255, 0.2) !important;
        border: 2px solid #818cf8 !important;
        box-shadow: 0 0 0 4px rgba(129, 140, 248, 0.2) !important;
        outline: none;
        color: white !important;
    }

    .main-btn {
        background-color: #4f46e5;
        border: none;
        border-radius: 12px;
        transition: all 0.3s ease;
    }

    .main-btn:hover {
        background-color: #4338ca !important;
        transform: translateY(-2px);
        box-shadow: 0 8px 15px rgba(79, 70, 229, 0.3);
    }
</style>

@endsection