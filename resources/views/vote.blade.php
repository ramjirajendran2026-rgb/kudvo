@extends('layouts.app')

<style>
    .form-control {
        width: 100% !important;
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
                    
                </span>
                <h2 class="fw-bold text-dark mt-3">Welcome For Voting </h2>
                <br>
               <a href="    vote" class="px-5 py-2 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-700 rounded-md transition duration-150 ease-in-out">Vote now</a>
            </div>


            
        </div>
    </div>
</div>

<style>
    /* Focus and Hover states to match your theme */
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