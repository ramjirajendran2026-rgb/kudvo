@extends('layouts.app')

<style>
    /* Styling for the dropdown to match your theme */
    select.form-control {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e");
        background-repeat: no-repeat;
        background-position: right 1rem center;
        background-size: 16px 12px;
    }

    .form-control {
        width: 100% !important; /* Set to 100% to fill the card container */
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 12px 16px;
        font-size: 16px;
        color: #333;
        transition: all 0.3s ease-in-out;
    }
</style>

@section('content')

<div class="bg-light d-flex justify-content-center align-items-center min-vh-100 py-5">
    <div class="card shadow-lg border-0 rounded-4 mx-auto" style="max-width: 480px; width: 100%;">
        
        <div style="height: 6px; background-color: #4f46e5; border-radius: 4px 4px 0 0;"></div>

        <div class="card-body p-5 bg-white d-flex flex-column align-items-center text-center">
            
            <div class="mb-4">
                <span class="p-3 rounded-circle bg-light d-inline-block">
                    <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" fill="#4f46e5" class="bi bi-person-plus-fill" viewBox="0 0 16 16">
                        <path d="M1 14s-1 0-1-1 1-4 6-4 6 3 6 4-1 1-1 1zm5-6a3 3 0 1 0 0-6 3 3 0 0 0 0 6"/>
                    </svg>
                </span>
                <h2 class="fw-bold text-dark mt-3">Create Account</h2>
                <p class="text-muted small">Join Kudvo to start voting</p>
            </div>

            <form action="/register" method="POST" class="w-100">
                @csrf
                
                <div class="mb-3 text-start"> 
                    <label class="form-label small fw-bold text-secondary">FULL NAME</label>
                    <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label small fw-bold text-secondary">EMAIL ADDRESS</label>
                    <input type="email" name="email" class="form-control" placeholder="name@example.com" required>
                </div>

                <div class="mb-3 text-start">
                    <label class="form-label small fw-bold text-secondary">ORGANISATION</label>
                    <select name="organisation_id" class="form-control" required>
                        <option value="" disabled selected>Select your organisation</option>
                 
                 @isset($organisations)
            @foreach($organisations as $org)
                <option value="{{ $org->id }}">
                    {{ $org->code }}
                </option>
            @endforeach
        @endisset
    </select></div>

                <div class="mb-3 text-start">
                    <label class="form-label small fw-bold text-secondary">PASSWORD</label><br>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" required>
                </div>

   <br>
                <div class="d-grid gap-2 mt-4">
                    <button type="submit" class="btn btn-lg text-white fw-bold shadow-sm main-btn" style="padding: 12px;">
                        Sign Up Now
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    /* Premium Look Enhancements */
    .form-control:focus {
        background-color: #fff !important;
        border: 2px solid #4f46e5 !important;
        box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1) !important;
        outline: none;
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