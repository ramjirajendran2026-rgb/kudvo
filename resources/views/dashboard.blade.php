@extends('layouts.admin.app')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center bg-white p-3 rounded shadow-sm mb-4">
        <h5 class="fw-bold mb-0 text-primary text-uppercase">
            <i class="bi bi-pencil-square me-2"></i>Calicut Lions Club
        </h5>
        <div class="badge bg-dark px-3 py-2">Voting Event ID : 7805ish400</div>
    </div>

    <div class="row g-4">
        <div class="col-md-4">
            <div class="card card-custom bg-primary p-4 shadow-sm">
                <div class="d-flex justify-content-between mb-4">
                    <i class="bi bi-sliders2 fs-1"></i>
                    <i class="bi bi-box-arrow-up-right"></i>
                </div>
                <h5 class="fw-bold text-uppercase">Plans & Options</h5>
                <button class="btn btn-light w-100 fw-bold mt-3 py-2">CHOOSE PLAN</button>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-custom p-4 shadow-sm" style="background-color: #f39c12;">
                <div class="d-flex justify-content-between mb-4">
                    <i class="bi bi-award fs-1"></i>
                    <i class="bi bi-box-arrow-up-right"></i>
                </div>
                <h5 class="fw-bold text-uppercase">Positions</h5>
                <button class="btn btn-light w-100 fw-bold mt-3 py-2 text-warning">DEFINE POSITIONS</button>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-custom p-4 shadow-sm" style="background-color: #27ae60;">
                <div class="d-flex justify-content-between mb-4">
                    <i class="bi bi-person-badge fs-1"></i>
                    <i class="bi bi-box-arrow-up-right"></i>
                </div>
                <h5 class="fw-bold text-uppercase">Candidates</h5>
                <button class="btn btn-light w-100 fw-bold mt-3 py-2 text-success">VIEW BALLOT</button>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-custom p-4 shadow-sm" style="background-color: #e74c3c;">
                <div class="d-flex justify-content-between mb-4">
                    <i class="bi bi-people-fill fs-1"></i>
                    <i class="bi bi-box-arrow-up-right"></i>
                </div>
                <h5 class="fw-bold text-uppercase">Voting Members</h5>
                <button class="btn btn-light w-100 fw-bold mt-3 py-2 text-danger">UPLOAD DATA</button>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-custom p-4 shadow-sm" style="background-color: #badc58;">
                <div class="d-flex justify-content-between mb-4 text-dark">
                    <i class="bi bi-shield-lock fs-1"></i>
                    <i class="bi bi-box-arrow-up-right"></i>
                </div>
                <h5 class="fw-bold text-uppercase text-dark">Checkout</h5>
                <button class="btn btn-dark w-100 fw-bold mt-3 py-2">PAYMENT OPTIONS</button>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card card-custom p-4 shadow-sm" style="background-color: #9b59b6;">
                <div class="d-flex justify-content-between mb-4">
                    <i class="bi bi-clock-history fs-1"></i>
                    <i class="bi bi-box-arrow-up-right"></i>
                </div>
                <h5 class="fw-bold text-uppercase">Timing & Sending</h5>
                <button class="btn btn-light w-100 fw-bold mt-3 py-2" style="color: #9b59b6;">VOTING LINKS</button>
            </div>
        </div>
    </div>

    <div class="text-center mt-5">
        <button class="btn btn-warning btn-lg px-5 fw-bold text-white shadow" style="background-color: #d35400; border:none;">
            MONITOR VOTING PROGRESS
        </button>
        <p class="mt-4 text-muted small">&copy; 2026 Kuudvo Admin Panel. All Rights Reserved.</p>
    </div>
</div>
@endsection