@extends('admin.app')

@section('content')
<div class="topbar d-flex justify-content-between align-items-center shadow-sm mb-4 bg-white p-3 rounded">
    <div class="d-flex align-items-center">
        <h5 class="fw-bold mb-0 text-primary text-uppercase me-4">
            <i class="bi bi-pencil-square me-2"></i>Calicut Lions Club
        </h5>
        <div class="border-start ps-3 d-none d-md-block">
            <span class="text-muted small d-block" style="font-size: 0.7rem; letter-spacing: 0.5px;">SYSTEM OPERATOR</span>
            <span class="fw-bold text-dark">{{ session('admin_user_name', Auth::user()->name ?? 'Admin') }}</span> 
            <span class="text-muted mx-2">|</span>
            <span class="small text-secondary">{{ session('admin_user_email', Auth::user()->email ?? '') }}</span>
        </div>
    </div>

    <div class="d-flex align-items-center gap-3">
        <div class="badge bg-dark p-2" style="font-family: monospace;">Voting Event ID : 7805ish400</div>
        
        <form action="{{ route('admin.logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="btn btn-outline-danger btn-sm fw-bold px-3">
                <i class="bi bi-power me-1"></i> LOGOUT
            </button>
        </form>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 bg-primary text-white p-3 transition-hover">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <i class="bi bi-sliders2 fs-1"></i>
                <a href="#" class="text-white opacity-50"><i class="bi bi-box-arrow-up-right"></i></a>
            </div>
            <h5 class="fw-bold">PLANS & OPTIONS</h5>
            <button class="btn btn-light btn-sm mt-3 w-100 fw-bold py-2">CHOOSE PLAN</button>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 bg-warning text-white p-3 transition-hover">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <i class="bi bi-award fs-1"></i>
                <a href="#" class="text-white opacity-50"><i class="bi bi-box-arrow-up-right"></i></a>
            </div>
            <h5 class="fw-bold">POSITIONS</h5>
            <button class="btn btn-light btn-sm mt-3 w-100 fw-bold py-2 text-warning">DEFINE POSITIONS</button>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 bg-success text-white p-3 transition-hover">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <i class="bi bi-person-walking fs-1"></i>
                <a href="#" class="text-white opacity-50"><i class="bi bi-box-arrow-up-right"></i></a>
            </div>
            <h5 class="fw-bold">CANDIDATES1</h5>
            <button class="btn btn-light btn-sm mt-3 w-100 fw-bold py-2 text-success">VIEW BALLOT</button>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 bg-danger text-white p-3 transition-hover">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <i class="bi bi-people-fill fs-1"></i>
                <a href="#" class="text-white opacity-50"><i class="bi bi-box-arrow-up-right"></i></a>
            </div>
            <h5 class="fw-bold">VOTING MEMBERS</h5>
            <button class="btn btn-light btn-sm mt-3 w-100 fw-bold py-2 text-danger">UPLOAD DATA</button>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 p-3 transition-hover" style="background-color: #badc58;">
            <div class="d-flex justify-content-between align-items-start mb-3 text-dark">
                <i class="bi bi-shield-lock fs-1"></i>
                <a href="#" class="text-dark opacity-50"><i class="bi bi-box-arrow-up-right"></i></a>
            </div>
            <h5 class="fw-bold text-dark text-uppercase">Checkout</h5>
            <button class="btn btn-dark btn-sm mt-3 w-100 fw-bold py-2">PAYMENT OPTIONS</button>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card h-100 shadow-sm border-0 p-3 transition-hover" style="background-color: #9b59b6; color: white;">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <i class="bi bi-clock-history fs-1"></i>
                <a href="#" class="text-white opacity-50"><i class="bi bi-box-arrow-up-right"></i></a>
            </div>
            <h5 class="fw-bold text-uppercase">Timing & Sending</h5>
            <button class="btn btn-light btn-sm mt-3 w-100 fw-bold py-2" style="color: #9b59b6;">VOTING LINKS</button>
        </div>
    </div>
</div>

<div class="row mt-5">
    <div class="col-md-12 text-center">
        <p class="text-muted small">&copy; 2026 Kuudvo Admin Panel. All Rights Reserved.</p>
    </div>
</div>

<style>
    .transition-hover {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.12) !important;
    }
</style>
@endsection