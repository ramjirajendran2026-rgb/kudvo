@extends('layouts.admin.app')

@section('title', 'Manage Organisations | kudvo')

@section('content')
    <div class="row mb-4">
        <div class="col">
            <h2 class="fw-bold">Organisations</h2>
            <p class="text-muted">Manage your registered entities below.</p>
        </div>
        <div class="col-auto">
            <button class="btn btn-primary px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#addModal">
                + Add Organisation
            </button>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-body p-0">
             </div>
    </div>
@endsection